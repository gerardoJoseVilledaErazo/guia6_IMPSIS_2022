<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profesores extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('form_validation');
        if (!isset($this->session->userdata['logged_in'])) {
            redirect("/");
        }
    }

    // FUNCIONES QUE CARGAN VISTAS /////////////////////////////////////////////////////////
    public function index()
    {
        $this->load->model('Profesor_model');
        $data = array(
            "records" => $this->Profesor_model->getAll(),
            "title" => "Profesores",
        );
        $this->load->view("shared/header", $data);
        $this->load->view("profesores/index", $data);
        $this->load->view("shared/footer");
    }

    public function insertar()
    {
        $this->load->model('Profesor_model');// //////////////////////////////////////////////////////
        $data = array(
            "profesores" => $this->Profesor_model->getAll(),// /////////////////////////////////////////
            "title" => "Insertar profesor",
        );
        $this->load->view("shared/header", $data);
        $this->load->view("profesores/add_edit", $data);
        $this->load->view("shared/footer");
    }

    public function modificar($id)
    {
        $this->load->model('Profesor_model');
        $profesor = $this->Profesor_model->getById($id);
        $data = array(
            "profesores" => $this->Profesor_model->getAll(),// ///////////////////////////////////////////
            "profesor" => $profesor,
            "title" => "Modificar profesor",
        );
        $this->load->view("shared/header", $data);
        $this->load->view("profesores/add_edit", $data);
        $this->load->view("shared/footer");
    }
    // FIN - FUNCIONES QUE CARGAN VISTAS /////////////////////////////////////////////////////////

    // FUNCIONES QUE REALIZAN OPERACIONES /////////////////////////////////////////////////////////
    public function add()
    {

        // Reglas de validaci??n del formulario
        /*
        required: indica que el campo es obligatorio.
        min_length: indica que la cadena debe tener al menos una cantidad determinada de caracteres.
        max_length: indica que la cadena debe tener como m??ximo una cantidad determinada de caracteres.
        valid_email: indica que el valor debe ser un correo con formato v??lido.
         */
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules(
            "idprofesor", 
            "Id Profesor", 
            "required|min_length[10]|max_length[10]|is_unique[profesores.idprofesor]"
        );
        $this->form_validation->set_rules(
            "nombre", 
            "Nombre", 
            "required|max_length[100]"
        );
        $this->form_validation->set_rules(
            "apellido", 
            "Apellido", 
            "required|max_length[100]"
        );
        $this->form_validation->set_rules(
            "fecha_nacimiento", 
            "Fecha de nacimiento", 
            "required|max_length[100]"
        );
        $this->form_validation->set_rules(
            "profesion", 
            "Profesion", 
            "required|max_length[100]"
        );
        $this->form_validation->set_rules(
            "genero", 
            "Genero", 
            "required|min_length[1]|max_length[1]"
        );
        $this->form_validation->set_rules(
            "email", 
            "Email", 
            "required|valid_email|max_length[150]|is_unique[profesores.email]"
        );

        // Modificando el mensaje de validaci??n para los errores
        $this->form_validation->set_message(
            'required', 
            'El campo %s es requerido.'
        );
        $this->form_validation->set_message(
            'min_length', 
            'El campo %s debe tener al menos %s caracteres.'
        );
        $this->form_validation->set_message(
            'max_length', 
            'El campo %s debe tener como m??ximo %s caracteres.'
        );
        $this->form_validation->set_message(
            'valid_email', 
            'El campo %s no es un correo v??lido.'
        );
        $this->form_validation->set_message(
            'is_unique', 
            'El campo %s ya existe.'
        );
        $this->form_validation->set_message(
            'alpha', 
            'El campo %s debe contener solo caracteres alfabeticos.'
        );

        // Par??metros de respuesta
        header('Content-type: application/json');
        $statusCode = 200;
        $msg = "";

        // Se ejecuta la validaci??n de los campos
        if ($this->form_validation->run()) {
            // Si la validaci??n es correcta entra ac??
            try {
                $this->load->model('Profesor_model');
                $data = array(
                    "idprofesor" => $this->input->post("idprofesor"),
                    "nombre" => $this->input->post("nombre"),
                    "apellido" => $this->input->post("apellido"),
                    "fecha_nacimiento" => $this->input->post("fecha_nacimiento"),
                    "profesion" => $this->input->post("profesion"),
                    "genero" => $this->input->post("genero"),
                    "email" => $this->input->post("email"),
                );
                $rows = $this->Profesor_model->insert($data);
                if ($rows > 0) {
                    $msg = "Informaci??n guardada correctamente.";
                } else {
                    $statusCode = 500;
                    $msg = "No se pudo guardar la informaci??n.";
                }
            } catch (Exception $ex) {
                $statusCode = 500;
                $msg = "Ocurri?? un error." . $ex->getMessage();
            }
        } else {
            // Si la validaci??n da error, entonces se ejecuta ac??
            $statusCode = 400;
            $msg = "Ocurrieron errores de validaci??n.";
            $errors = array();
            foreach ($this->input->post() as $key => $value) {
                $errors[$key] = form_error($key);
            }
            $this->data['errors'] = $errors;
        }
        // Se asigna el mensaje que llevar?? la respuesta
        $this->data['msg'] = $msg;
        // Se asigna el c??digo de Estado HTTP
        $this->output->set_status_header($statusCode);
        // Se env??a la respuesta en formato JSON
        echo json_encode($this->data);

    }

    public function update()
    {

        // Reglas de validaci??n del formulario
        /*
        required: indica que el campo es obligatorio.
        min_length: indica que la cadena debe tener al menos una cantidad determinada de caracteres.
        max_length: indica que la cadena debe tener como m??ximo una cantidad determinada de caracteres.
        valid_email: indica que el valor debe ser un correo con formato v??lido.
         */
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules(
            "idprofesor", 
            "Id Profesor", 
            "required|min_length[10]|max_length[10]"
        );
        $this->form_validation->set_rules(
            "nombre", 
            "Nombre", 
            "required|max_length[100]"
        );
        $this->form_validation->set_rules(
            "apellido", 
            "Apellido", 
            "required|max_length[100]"
        );
        $this->form_validation->set_rules(
            "fecha_nacimiento", 
            "Fecha de nacimiento", 
            "required|max_length[100]"
        );
        $this->form_validation->set_rules(
            "profesion", 
            "Profesion", 
            "required|max_length[100]"
        );
        $this->form_validation->set_rules(
            "genero", 
            "Genero", 
            "required|min_length[1]|max_length[1]"
        );
        $this->form_validation->set_rules(
            "email", 
            "Email", 
            "required|valid_email|max_length[150]"
        );

        // Modificando el mensaje de validaci??n para los errores, en este caso para
        // la regla required, min_length, max_length
        $this->form_validation->set_message('required', 
        'El campo %s es requerido.');
        $this->form_validation->set_message('min_length', 
        'El campo %s debe tener al menos %s caracteres.');
        $this->form_validation->set_message('max_length', 
        'El campo %s debe tener como m??ximo %s caracteres.');
        $this->form_validation->set_message('is_unique', 
        'El campo %s ya existe.');
        $this->form_validation->set_message('alpha', 
        'El campo %s debe contener solo caracteres alfabeticos.');

        // Par??metros de respuesta
        header('Content-type: application/json');
        $statusCode = 200;
        $msg = "";

        // Se ejecuta la validaci??n de los campos
        if ($this->form_validation->run()) {
            // Si la validaci??n es correcta entra
            try {
                $this->load->model('Profesor_model');
                $data = array(
                    "idprofesor" => $this->input->post("idprofesor"),
                    "nombre" => $this->input->post("nombre"),
                    "apellido" => $this->input->post("apellido"),
                    "fecha_nacimiento" => $this->input->post("fecha_nacimiento"),
                    "profesion" => $this->input->post("profesion"),
                    "genero" => $this->input->post("genero"),
                    "email" => $this->input->post("email"),
                );
                $rows = $this->Profesor_model->update($data, $this->input->post("PK_profesor"));
                $msg = "Informaci??n guardada correctamente.";
            } catch (Exception $ex) {
                $statusCode = 500;
                $msg = "Ocurri?? un error." . $ex->getMessage();
            }
        } else {
            // Si la validaci??n da error, entonces se ejecuta ac??
            $statusCode = 400;
            $msg = "Ocurrieron errores de validaci??n.";
            $errors = array();
            foreach ($this->input->post() as $key => $value) {
                $errors[$key] = form_error($key);
            }
            $this->data['errors'] = $errors;
        }
        // Se asigna el mensaje que llevar?? la respuesta
        $this->data['msg'] = $msg;
        // Se asigna el c??digo de Estado HTTP
        $this->output->set_status_header($statusCode);
        // Se env??a la respuesta en formato JSON
        echo json_encode($this->data);
    }

    public function eliminar($id)
    {
        $this->load->model('Profesor_model');
        $result = $this->Profesor_model->delete($id);
        if ($result) {
            $this->session->set_flashdata('success', "Registro borrado correctamente.");
        } else {
            $this->session->set_flashdata('error', "No se pudo borrar el registro.");
        }
        redirect("profesores");
    }
    // FIN - FUNCIONES QUE REALIZAN OPERACIONES /////////////////////////////////////////////////////////

}
