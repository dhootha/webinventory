<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    /*
     * Holds account status list
     * 
     * $var array
     */
    protected $user_group;
    protected $account_status_list;
    public $user_type;
    public $login_uri;
    public $login_success_uri;
    public $login_template;
    public $login_view;

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->helper('url');

        // Load MongoDB library instead of native db driver if required
        $this->config->item('use_mongodb', 'ion_auth') ?
                        $this->load->library('mongo_db') :
                        $this->load->database();

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
        $this->load->helper('language');

        $this->user_type = CUSTOMER;
        $this->user_group = $this->config->item('user_group', 'ion_auth');
        $this->account_status_list = $this->config->item('account_status', 'ion_auth');
    }

    function salesman_login() {

        $this->user_type = SALESMAN;

        $this->login_success_uri = SALESMAN_LOGIN_SUCCESS_URI;
        $this->login_uri = SALESMAN_LOGIN_URI;
        $this->login_template = SALESMAN_LOGIN_TEMPLATE;
        $this->login_view = SALESMAN_LOGIN_VIEW;

        if (!$this->ion_auth->logged_in()) {
            $this->login();
        } else {
            //set the
            //set flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->template->load(SALESMAN_LOGIN_SUCCESS_TEMPLATE, SALESMAN_LOGIN_SUCCESS_VIEW, $this->data);
        }
    }

    function customer_login() {

        $this->user_type = CUSTOMER;

        $this->login_success_uri = CUSTOMER_LOGIN_SUCCESS_URI;
        $this->login_uri = CUSTOMER_LOGIN_URI;
        $this->login_template = CUSTOMER_LOGIN_TEMPLATE;
        $this->login_view = CUSTOMER_LOGIN_VIEW;

        if (!$this->ion_auth->logged_in()) {
            $this->login();
        } else {
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->template->load(CUSTOMER_LOGIN_SUCCESS_TEMPLATE, CUSTOMER_LOGIN_SUCCESS_VIEW, $this->data);
        }
    }

    public function login() {
        $this->data['title'] = "Login";

        //validate form input
        $this->form_validation->set_rules('identity', 'Identity', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == true) {
            //check to see if the user is logging in
            //check for "remember me"
            $remember = (bool) $this->input->post('remember');

            if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $this->user_type, $remember)) {
                //if the login is successful
                //redirect them back to the home page
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect($this->login_success_uri, 'refresh');
            } else {
                //if the login was un-successful
                //redirect them back to the login page
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect($this->login_uri, 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
            }
        } else {
            //the user is not logging in so display the login page
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $this->data['identity'] = array('name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'value' => $this->form_validation->set_value('identity'),
            );
            $this->data['password'] = array('name' => 'password',
                'id' => 'password',
                'type' => 'password',
            );
            $this->template->load($this->login_template, $this->login_view, $this->data);
        }
    }

    //log the user out
    function logout() {
        $this->data['title'] = "Logout";

        //log the user out
        $this->ion_auth->logout();

        //redirect them to the login page
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        redirect('nonmember', 'refresh');
    }

    //create a new user
    function signup() {
        $this->data['title'] = "Create User";

        //validate form input
        $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');
        $this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'required|xss_clean');
        $this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

        if ($this->form_validation->run() == true) {
            $username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'company' => $this->input->post('company'),
                'phone' => $this->input->post('phone'),
            );
        }
        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data)) {
            //check to see if we are creating the user
            //redirect them back to the admin page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("nonmember", 'refresh');
        } else {
            //display the create user form
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

            $this->data['first_name'] = array(
                'name' => 'first_name',
                'id' => 'first_name',
                'type' => 'text',
                'value' => $this->form_validation->set_value('first_name'),
            );
            $this->data['last_name'] = array(
                'name' => 'last_name',
                'id' => 'last_name',
                'type' => 'text',
                'value' => $this->form_validation->set_value('last_name'),
            );
            $this->data['email'] = array(
                'name' => 'email',
                'id' => 'email',
                'type' => 'text',
                'value' => $this->form_validation->set_value('email'),
            );
            $this->data['company'] = array(
                'name' => 'company',
                'id' => 'company',
                'type' => 'text',
                'value' => $this->form_validation->set_value('company'),
            );
            $this->data['phone'] = array(
                'name' => 'phone',
                'id' => 'phone',
                'type' => 'text',
                'value' => $this->form_validation->set_value('phone'),
            );
            $this->data['password'] = array(
                'name' => 'password',
                'id' => 'password',
                'type' => 'password',
                'value' => $this->form_validation->set_value('password'),
            );
            $this->data['password_confirm'] = array(
                'name' => 'password_confirm',
                'id' => 'password_confirm',
                'type' => 'password',
                'value' => $this->form_validation->set_value('password_confirm'),
            );

            $this->template->load(null, 'create_user', $this->data);
        }
    }
    
        //edit a user
    function update_profile($id = null) {
        $this->data['title'] = "Update Profile";

        if (!$this->ion_auth->logged_in()) {
            redirect('nonmember', 'refresh');
        }
        
        if(!$this->ion_auth->is_admin()){
            $id = $this->ion_auth->get_user_id();
        }
        
        $user = $this->ion_auth->user($id)->row();
        $groups = $this->ion_auth->groups()->result_array();
        $currentGroups = $this->ion_auth->get_users_groups($id)->result();

        //validate form input
        $this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required|xss_clean');
        $this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'required|xss_clean');
        $this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'required|xss_clean');
        $this->form_validation->set_rules('groups', $this->lang->line('edit_user_validation_groups_label'), 'xss_clean');

        if (isset($_POST) && !empty($_POST)) {
            // do we have a valid request?
            if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
                show_error($this->lang->line('error_csrf'));
            }

            $data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'company' => $this->input->post('company'),
                'phone' => $this->input->post('phone'),
            );

            //Update the groups user belongs to
            $groupData = $this->input->post('groups');

            if (isset($groupData) && !empty($groupData)) {

                $this->ion_auth->remove_from_group('', $id);

                foreach ($groupData as $grp) {
                    $this->ion_auth->add_to_group($grp, $id);
                }
            }

            //update the password if it was posted
            if ($this->input->post('password')) {
                $this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
                $this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');

                $data['password'] = $this->input->post('password');
            }

            if ($this->form_validation->run() === TRUE) {
                $this->ion_auth->update($user->id, $data);

                //check to see if we are creating the user
                //redirect them back to the admin page
                $this->session->set_flashdata('message', "User Saved");
                redirect("nonmember", 'refresh');
            }
        }

        //display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        //set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

        //pass the user to the view
        $this->data['user'] = $user;
        $this->data['groups'] = $groups;
        $this->data['currentGroups'] = $currentGroups;

        $this->data['first_name'] = array(
            'name' => 'first_name',
            'id' => 'first_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('first_name', $user->first_name),
        );
        $this->data['last_name'] = array(
            'name' => 'last_name',
            'id' => 'last_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('last_name', $user->last_name),
        );
        $this->data['company'] = array(
            'name' => 'company',
            'id' => 'company',
            'type' => 'text',
            'value' => $this->form_validation->set_value('company', $user->company),
        );
        $this->data['phone'] = array(
            'name' => 'phone',
            'id' => 'phone',
            'type' => 'text',
            'value' => $this->form_validation->set_value('phone', $user->phone),
        );
        $this->data['password'] = array(
            'name' => 'password',
            'id' => 'password',
            'type' => 'password'
        );
        $this->data['password_confirm'] = array(
            'name' => 'password_confirm',
            'id' => 'password_confirm',
            'type' => 'password'
        );

        $this->template->load(null, 'edit_user', $this->data);
    }

        //change password
    function change_password() {
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

        if (!$this->ion_auth->logged_in()) {
            redirect('nonmember', 'refresh');
        }

        $user = $this->ion_auth->user()->row();

        if ($this->form_validation->run() == false) {
            //display the form
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
            $this->data['old_password'] = array(
                'name' => 'old',
                'id' => 'old',
                'type' => 'password',
            );
            $this->data['new_password'] = array(
                'name' => 'new',
                'id' => 'new',
                'type' => 'password',
                'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
            );
            $this->data['new_password_confirm'] = array(
                'name' => 'new_confirm',
                'id' => 'new_confirm',
                'type' => 'password',
                'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
            );
            $this->data['user_id'] = array(
                'name' => 'user_id',
                'id' => 'user_id',
                'type' => 'hidden',
                'value' => $user->id,
            );

            //render
            $this->template->load(null, 'change_password', $this->data);
        } else {
            $identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));

            $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

            if ($change) {
                //if the password was successfully changed
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->logout();
            } else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect('user/change_password', 'refresh');
            }
        }
    }
    
    // create a new group
    function create_group() {
        $this->data['title'] = $this->lang->line('create_group_title');

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            redirect('auth', 'refresh');
        }

        //validate form input
        $this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'required|alpha_dash|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('create_group_validation_desc_label'), 'xss_clean');

        if ($this->form_validation->run() == TRUE) {
            $new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
            if ($new_group_id) {
                // check to see if we are creating the group
                // redirect them back to the admin page
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect("auth", 'refresh');
            }
        } else {
            //display the create group form
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

            $this->data['group_name'] = array(
                'name' => 'group_name',
                'id' => 'group_name',
                'type' => 'text',
                'value' => $this->form_validation->set_value('group_name'),
            );
            $this->data['description'] = array(
                'name' => 'description',
                'id' => 'description',
                'type' => 'text',
                'value' => $this->form_validation->set_value('description'),
            );

            $this->_render_page('auth/create_group', $this->data);
        }
    }

    //forgot password
    function forgot_password() {
        $this->form_validation->set_rules('email', $this->lang->line('forgot_password_validation_email_label'), 'required');
        if ($this->form_validation->run() == false) {
            //setup the input
            $this->data['email'] = array('name' => 'email',
                'id' => 'email',
            );

            if ($this->config->item('identity', 'ion_auth') == 'username') {
                $this->data['identity_label'] = $this->lang->line('forgot_password_username_identity_label');
            } else {
                $this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
            }

            //set any errors and display the form
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->template->load(null, 'forgot_password', $this->data);
        } else {
            // get identity for that email
            $config_tables = $this->config->item('tables', 'ion_auth');
            $identity = $this->db->where('email', $this->input->post('email'))->limit('1')->get($config_tables['users'])->row();

            //run the forgotten password method to email an activation code to the user
            $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

            if ($forgotten) {
                //if there were no errors
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect("user/login", 'refresh'); //we should display a confirmation page here instead of the login page
            } else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect("user/forgot_password", 'refresh');
            }
        }
    }

    //reset password - final step for forgotten password
    public function reset_password($code = NULL) {
        if (!$code) {
            show_404();
        }

        $user = $this->ion_auth->forgotten_password_check($code);

        if ($user) {
            //if the code is valid then display the password reset form

            $this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

            if ($this->form_validation->run() == false) {
                //display the form
                //set the flash data error message if there is one
                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

                $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
                $this->data['new_password'] = array(
                    'name' => 'new',
                    'id' => 'new',
                    'type' => 'password',
                    'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
                );
                $this->data['new_password_confirm'] = array(
                    'name' => 'new_confirm',
                    'id' => 'new_confirm',
                    'type' => 'password',
                    'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
                );
                $this->data['user_id'] = array(
                    'name' => 'user_id',
                    'id' => 'user_id',
                    'type' => 'hidden',
                    'value' => $user->id,
                );
                $this->data['csrf'] = $this->_get_csrf_nonce();
                $this->data['code'] = $code;

                //render
                $this->_render_page('auth/reset_password', $this->data);
            } else {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id')) {

                    //something fishy might be up
                    $this->ion_auth->clear_forgotten_password_code($code);

                    show_error($this->lang->line('error_csrf'));
                } else {
                    // finally change the password
                    $identity = $user->{$this->config->item('identity', 'ion_auth')};

                    $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

                    if ($change) {
                        //if the password was successfully changed
                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        $this->logout();
                    } else {
                        $this->session->set_flashdata('message', $this->ion_auth->errors());
                        redirect('auth/reset_password/' . $code, 'refresh');
                    }
                }
            }
        } else {
            //if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }
    }

    //activate the user
    function activate($id, $code = false) {
        if ($code !== false) {
            $activation = $this->ion_auth->activate($id, $code);
        } else if ($this->ion_auth->is_admin()) {
            $activation = $this->ion_auth->activate($id);
        }

        if ($activation) {
            //redirect them to the auth page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("auth", 'refresh');
        } else {
            //redirect them to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }
    }

    //deactivate the user
    function deactivate($id = NULL) {
        $id = $this->config->item('use_mongodb', 'ion_auth') ? (string) $id : (int) $id;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
        $this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');

        if ($this->form_validation->run() == FALSE) {
            // insert csrf check
            $this->data['csrf'] = $this->_get_csrf_nonce();
            $this->data['user'] = $this->ion_auth->user($id)->row();

            $this->_render_page('auth/deactivate_user', $this->data);
        } else {
            // do we really want to deactivate?
            if ($this->input->post('confirm') == 'yes') {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
                    show_error($this->lang->line('error_csrf'));
                }

                // do we have the right userlevel?
                if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
                    $this->ion_auth->deactivate($id);
                }
            }

            //redirect them back to the auth page
            redirect('auth', 'refresh');
        }
    }


    //edit a group
    function edit_group($id) {
        // bail if no group id given
        if (!$id || empty($id)) {
            redirect('auth', 'refresh');
        }

        $this->data['title'] = $this->lang->line('edit_group_title');

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            redirect('auth', 'refresh');
        }

        $group = $this->ion_auth->group($id)->row();

        //validate form input
        $this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'required|alpha_dash|xss_clean');
        $this->form_validation->set_rules('group_description', $this->lang->line('edit_group_validation_desc_label'), 'xss_clean');

        if (isset($_POST) && !empty($_POST)) {
            if ($this->form_validation->run() === TRUE) {
                $group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']);

                if ($group_update) {
                    $this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
                } else {
                    $this->session->set_flashdata('message', $this->ion_auth->errors());
                }
                redirect("auth", 'refresh');
            }
        }

        //set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

        //pass the user to the view
        $this->data['group'] = $group;

        $this->data['group_name'] = array(
            'name' => 'group_name',
            'id' => 'group_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('group_name', $group->name),
        );
        $this->data['group_description'] = array(
            'name' => 'group_description',
            'id' => 'group_description',
            'type' => 'text',
            'value' => $this->form_validation->set_value('group_description', $group->description),
        );

        $this->_render_page('auth/edit_group', $this->data);
    }

    function _get_csrf_nonce() {
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return array($key => $value);
    }

    function _valid_csrf_nonce() {
        if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
                $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function create_supplier()
    {
        $this->data['message'] = '';
        $this->form_validation->set_error_delimiters("<div style='color:red'>", '</div>');
        $this->form_validation->set_rules('user_name', 'User Name', 'xss_clean|required');
        $this->form_validation->set_rules('password', 'Password', 'xss_clean|required');
        $this->form_validation->set_rules('email', 'Email', 'xss_clean|required');
        $this->form_validation->set_rules('company_name', 'Supplier Name', 'xss_clean|required');
        if ($this->input->post('submit_create_supplier')) 
        {
            if ($this->form_validation->run() == true) 
            {
                $user_name = $this->input->post('user_name');
                $password =$this->input->post('password');
                $email = $this->input->post('email');
                $additional_data = array(
                    'account_status_id' => 1,
                    'created_date' => date('Y-m-d H:i:s')
                );
                $groups = array(
                    'id' => 4
                );
                $user_id = $this->ion_auth->register($user_name, $password, $email, $additional_data, $groups);
                $supplier_data = array(
                    'user_id' => $user_id,
                    'company' => $this->input->post('company_name')
                );
                $this->ion_auth->create_supplier($supplier_data);
                //redirect('','refresh');
            }
            else
            {
                $this->data['message'] = validation_errors();
            }
        }
        $this->data['user_name'] = array(
            'name' => 'user_name',
            'id' => 'user_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('user_name'),
        );
        $this->data['password'] = array(
            'name' => 'password',
            'id' => 'password',
            'type' => 'password',
            'value' => '',
        );
        $this->data['email'] = array(
            'name' => 'email',
            'id' => 'email',
            'type' => 'text',
            'value' => $this->form_validation->set_value('email'),
        );
        $this->data['company_name'] = array(
            'name' => 'company_name',
            'id' => 'company_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('company_name'),
        );
        $this->data['submit_create_supplier'] = array(
            'name' => 'submit_create_supplier',
            'id' => 'submit_create_supplier',
            'type' => 'submit',
            'value' => 'Add',
        );
        
        $this->template->load(null, 'supplier/create_supplier', $this->data);
    }
    
    public function create_customer()
    {
        $this->template->load(null, 'customer/create_customer');
    }
    
    public function create_customer_sale_order()
    {
        $response = array();
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $phone_no = $_POST['phone_no'];
        $card_no = $_POST['card_no'];;
        $user_name = $_POST['phone_no'];
        $password = "password";
        $email = "dummy@dummy.com";
        $additional_data = array(
            'account_status_id' => $this->account_status_list['active_id'],
            'first_name' => $first_name,
            'last_name' => $last_name,
            'phone' => $phone_no,
            'created_date' => date('Y-m-d H:i:s')
        );
        $groups = array('id' => $this->user_group['customer_id']);
        $user_id = $this->ion_auth->register($user_name, $password, $email, $additional_data, $groups);
        if( $user_id >= 0 )
        {
            $additional_data = array(
                'user_id' => $user_id,
                'card_no' => $card_no
            );
            $id = $this->ion_auth->create_customer($additional_data);
            $customer_info_array = $this->ion_auth->get_customer($user_id)->result_array();
            $customer_info = array();
            if( count($customer_info_array) > 0 )
            {
                $customer_info = $customer_info_array[0];
            }
            $response['status'] = '1';
            $response['customer_info'] = $customer_info;
        } 
        else
        {
            $response['status'] = '0';
        }
        echo json_encode($response);
    }
    public function show_customer($user_id)
    {
        $customer_info_array = $this->ion_auth->get_customer($user_id)->result_array();
        $customer_info = array();
        if( count($customer_info_array) > 0 )
        {
            $customer_info = $customer_info_array[0];
            print_r($customer_info_array[0]);
        }
    }

}
