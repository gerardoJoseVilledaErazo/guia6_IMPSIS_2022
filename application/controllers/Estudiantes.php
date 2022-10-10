<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Estudiantes extends CI_Controller
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
        $this->load->model('Estudiante_model');
        $data = array(
            "records" => $this->Estudiante_model->getAll(),
            "title" => "Estudiantes",
        );
        $this->load->view("shared/header", $data);
        $this->load->view("estudiantes/index", $data);
        $this->load->view("shared/footer");
    }

    public function insertar()
    {
        $this->load->model('Carrera_model');
        $data = array(
            "carreras" => $this->Carrera_model->getAll(),
            "title" => "Insertar estudiante",
        );
        $this->load->view("shared/header", $data);
        $this->load->view("estudiantes/add_edit", $data);
        $this->load->view("shared/footer");
    }

    public function modificar($id)
    {
        $this->load->model('Carrera_model');
        $this->load->model('Estudiante_model');
        $estudiante = $this->Estudiante_model->getById($id);
        $data = array(
            "carreras" => $this->Carrera_model->getAll(),
            "estudiante" => $estudiante,
            "title" => "Modificar estudiante",
        );
        $this->load->view("shared/header", $data);
        $this->load->view("estudiantes/add_edit", $data);
        $this->load->view("shared/footer");
    }
    // FIN - FUNCIONES QUE CARGAN VISTAS /////////////////////////////////////////////////////////

    // FUNCIONES QUE REALIZAN OPERACIONES /////////////////////////////////////////////////////////
    public function add()
    {

        // Reglas de validación del formulario
        /*
        required: indica que el campo es obligatorio.
        min_length: indica que la cadena debe tener al menos una cantidad determinada de caracteres.
        max_length: indica que la cadena debe tener como máximo una cantidad determinada de caracteres.
        valid_email: indica que el valor debe ser un correo con formato válido.
         */
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules(
            "idestudiante", 
            "Id Estudiante", 
            "required|min_length[12]|max_length[12]|is_unique[estudiantes.idestudiante]"
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
            "email", 
            "Email", 
            "required|valid_email|max_length[100]|is_unique[estudiantes.email]"
        );
        $this->form_validation->set_rules(
            "idcarrera", 
            "Carrera", 
            "required|min_length[3]|max_length[3]"
        );
        $this->form_validation->set_rules(
            "usuario", 
            "Usuario", 
            "required|max_length[100]|is_unique[estudiantes.usuario]"
        );

        // Modificando el mensaje de validación para los errores
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
            'El campo %s debe tener como máximo %s caracteres.'
        );
        $this->form_validation->set_message(
            'valid_email', 
            'El campo %s no es un correo válido.'
        );
        $this->form_validation->set_message(
            'is_unique', 
            'El campo %s ya existe.'
        );
        $this->form_validation->set_message(
            'alpha', 
            'El campo %s debe contener solo caracteres alfabeticos.'
        );

        // Parámetros de respuesta
        header('Content-type: application/json');
        $statusCode = 200;
        $msg = "";

        // Se ejecuta la validación de los campos
        if ($this->form_validation->run()) {
            // Si la validación es correcta entra acá
            try {
                $this->load->model('Estudiante_model');
                $data = array(
                    "idestudiante" => $this->input->post("idestudiante"),
                    "nombre" => $this->input->post("nombre"),
                    "apellido" => $this->input->post("apellido"),
                    "email" => $this->input->post("email"),
                    "usuario" => $this->input->post("usuario"),
                    "idcarrera" => $this->input->post("idcarrera"),
                );
                $rows = $this->Estudiante_model->insert($data);
                if ($rows > 0) {
                    $msg = "Información guardada correctamente.";
                } else {
                    $statusCode = 500;
                    $msg = "No se pudo guardar la información.";
                }
            } catch (Exception $ex) {
                $statusCode = 500;
                $msg = "Ocurrió un error." . $ex->getMessage();
            }
        } else {
            // Si la validación da error, entonces se ejecuta acá
            $statusCode = 400;
            $msg = "Ocurrieron errores de validación.";
            $errors = array();
            foreach ($this->input->post() as $key => $value) {
                $errors[$key] = form_error($key);
            }
            $this->data['errors'] = $errors;
        }
        // Se asigna el mensaje que llevará la respuesta
        $this->data['msg'] = $msg;
        // Se asigna el código de Estado HTTP
        $this->output->set_status_header($statusCode);
        // Se envía la respuesta en formato JSON
        echo json_encode($this->data);

    }

    public function update()
    {

        // Reglas de validación del formulario
        $this->form_validation->set_error_delimiters('', '');
        /*
        required: indica que el campo es obligatorio.
        min_length: indica que la cadena debe tener al menos una cantidad determinada de caracteres.
        max_length: indica que la cadena debe tener como máximo una cantidad determinada de caracteres.
        valid_email: indica que el valor debe ser un correo con formato válido.
         */
        $this->form_validation->set_rules(
            "idestudiante", 
            "Id Estudiante", 
            "required|min_length[12]|max_length[12]"
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
            "email", 
            "Email", 
            "required|valid_email|max_length[100]"
        );
        $this->form_validation->set_rules(
            "idcarrera", 
            "Carrera", 
            "required|min_length[3]|max_length[3]"
        );
        $this->form_validation->set_rules(
            "usuario", 
            "Usuario", 
            "required|max_length[100]"
        );

        // Modificando el mensaje de validación para los errores, 
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
            'El campo %s debe tener como máximo %s caracteres.'
        );
        $this->form_validation->set_message(
            'valid_email', 
            'El campo %s no es un correo válido.'
        );
        $this->form_validation->set_message(
            'is_unique', 
            'El campo %s ya existe.'
        );
        $this->form_validation->set_message(
            'alpha', 
            'El campo %s debe contener solo caracteres alfabeticos.'
        );

        // Parámetros de respuesta
        header('Content-type: application/json');
        $statusCode = 200;
        $msg = "";

        // Se ejecuta la validación de los campos
        if ($this->form_validation->run()) {
            // Si la validación es correcta entra
            try {
                $this->load->model('Estudiante_model');
                $data = array(
                    "idestudiante" => $this->input->post("idestudiante"),
                    "nombre" => $this->input->post("nombre"),
                    "apellido" => $this->input->post("apellido"),
                    "email" => $this->input->post("email"),
                    "usuario" => $this->input->post("usuario"),
                    "idcarrera" => $this->input->post("idcarrera"),
                );
                $rows = $this->Estudiante_model->update($data, $this->input->post("PK_estudiante"));
                $msg = "Información guardada correctamente.";
            } catch (Exception $ex) {
                $statusCode = 500;
                $msg = "Ocurrió un error." . $ex->getMessage();
            }
        } else {
            // Si la validación da error, entonces se ejecuta acá
            $statusCode = 400;
            $msg = "Ocurrieron errores de validación.";
            $errors = array();
            foreach ($this->input->post() as $key => $value) {
                $errors[$key] = form_error($key);
            }
            $this->data['errors'] = $errors;
        }
        // Se asigna el mensaje que llevará la respuesta
        $this->data['msg'] = $msg;
        // Se asigna el código de Estado HTTP
        $this->output->set_status_header($statusCode);
        // Se envía la respuesta en formato JSON
        echo json_encode($this->data);
    }

    public function eliminar($id)
    {
        $this->load->model('Estudiante_model');
        $result = $this->Estudiante_model->delete($id);
        if ($result) {
            $this->session->set_flashdata('success', "Registro borrado correctamente.");
        } else {
            $this->session->set_flashdata('error', "No se pudo borrar el registro.");
        }
        redirect("estudiantes");
    }
    // FIN - FUNCIONES QUE REALIZAN OPERACIONES /////////////////////////////////////////////////////////

}
