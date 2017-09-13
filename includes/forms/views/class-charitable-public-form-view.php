<?php
/**
 * Public form view.
 *
 * This is responsible for rendering the output of forms.
 *
 * @version   1.5.0
 * @package   Charitable/Forms/Charitable_Public_Form_View
 * @author    Eric Daams
 * @copyright Copyright (c) 2017, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Public_Form_View' ) ) :

    /**
     * Charitable_Public_Form_View.
     *
     * @since 1.5.0
     */
    class Charitable_Public_Form_View implements Charitable_Form_View_Interface {

        /**
         * The Form we are responsible for rendering.
         *
         * @since 1.5.0
         *
         * @var   Charitable_Form
         */
        protected $form;

        /**
         * Set up view instance.
         *
         * @since  1.5.0
         *
         * @param Charitable_Form $form Form object.
         */
        public function __construct( Charitable_Form $form ) {
            $this->form = $form;
        }

        /**
         * Render a form.
         *
         * @since  1.5.0
         *
         * @return void
         */
        public function render() {

        }

        /**
         * Render all of a form's fields.
         *
         * @since  1.5.0
         *
         * @return void
         */
        public function render_fields() {
            array_walk( $form->get_fields() )
        }

        /**
         * Render a specific form fields.
         *
         * @since  1.5.0
         *
         * @param  array  $field Field definition.
         * @param  string $key   Field key.
         * @param  array $args   Mixed array of arguments.
         * @return boolean       False if the field was not rendered. True otherwise.
         */
        public function render_field( $field, $key, $args = array() ) {
            if ( ! array_key_exists( 'type', $field ) ) {
                return false;
            }

            /* Get our index and namespace, with defaults if they're not provided. */
            $namespace = array_key_exists( 'namespace', $args ) ? $args['namespace'] : null;
            $index     = array_key_exists( 'index', $args ) ? $args['index'] : 0;

            /* Set up some form attributes. */
            $field['key']   = $this->get_field_name( $key, $namespace, $index );
            $field['attrs'] = array_key_exists( 'attrs', $field ) ? $field['attrs'] : array();

            /* Get the template and make sure it's valid. */
            $template = $this->get_field_template( $field, $index );

            if ( ! $template ) {
                return false;
            }

            $template->set_view_args( array(
                'form'    => $this->form,
                'field'   => $field,
                'classes' => $this->get_field_classes( $field, $index ),
            ) );

            $template->render();

            return true;
        }

        /**
         * Return the key for a particular field.
         *
         * @since  1.5.0
         *
         * @param  string      $key       Field key.
         * @param  string|null $namespace Namespace for the form field's name attribute.
         * @param  int         $index     The current index.         
         * @return string
         */
        protected function get_field_name( $key, $namespace = null, $index = 0 ) {
            $name = $key;

            if ( ! is_null( $namespace ) ) {
                $name = $namespace . '[' . $name . ']';
            }

            /**
             * Filter the name attribute to be used for the field.
             *
             * @since 1.0.0
             *
             * @param string          $name      The name attribute.
             * @param string          $key       The field's key.
             * @param string|null     $namespace Namespace for the field's attribute.
             * @param Charitable_Form $form      The Charitable_Form object.
             * @param int             $index     The current index.
             */
            return apply_filters( 'charitable_form_field_key', $name, $key, $namespace, $this->form, $index );
        }

        /**
         * Return a field template.
         *
         * @since  1.5.0
         *
         * @param  array $field              Field definition.
         * @param  int   $index              The current index.
         * @return Charitable_Template|false Returns a template if the template file exists. If it doesn't returns false.
         */
        public function get_field_template( $field, $index ) {
            /**
             * Filter the template to be used for the form.
             *
             * Any callback hooking into this filter should return a `Charitable_Template` 
             * instance. Anything else will be ignored.
             *
             * @since 1.0.0
             *
             * @param false|Charitable_Template $template False by default.
             * @param array                     $field    Field definition.
             * @param Charitable_Form           $form     The Charitable_Form object.
             * @param int                       $index    The current index.
             */
            $template = apply_filters( 'charitable_form_field_template', false, $field, $this->form, $index );

            /* Fall back to default Charitable_Template if no template returned or if template was not object of 'Charitable_Template' class. */
            if ( ! $this->is_valid_template( $template ) ) {
                $template = new Charitable_Template( $this->get_template_name( $field ), false );
            }

            if ( ! $template->template_file_exists() ) {
                return false;
            }

            return $template;
        }

        /**
         * Return the template name used for this field.
         *
         * @since  1.5.0
         *
         * @param  array $field Field definition.
         * @return string
         */
        public function get_template_name( $field ) {
            if ( $this->use_default_field_template( $field['type'] ) ) {
                $template_name = 'form-fields/default.php';
            } else {
                $template_name = 'form-fields/' . $field['type'] . '.php';
            }

            /**
             * Filter the template name.
             *
             * @since 1.0.0
             *
             * @param string $template_name Default template name.
             */
            return apply_filters( 'charitable_form_field_template_name', $template_name );
        }

        /**
         * Whether the given field type can use the default field template.
         *
         * @since  1.5.0
         *
         * @param  string $field_type Type of field.
         * @return boolean
         */
        protected function use_default_field_template( $field_type ) {
            /**
             * Filter the list of field types that use the default template.
             *
             * @since 1.0.0
             *
             * @param string[] $types Field types.
             */
            $default_field_types = apply_filters( 'charitable_default_template_field_types', array(
                'text',
                'email',
                'password',
                'date',
            ) );

            return in_array( $field_type, $default_field_types );
        }

        /**
         * Checks whether a template is valid.
         *
         * @since  1.5.0
         *
         * @param  mixed $template Template we're checking.
         * @return boolean
         */
        protected function is_valid_template( $template ) {
            return is_object( $template ) && is_a( $template, 'Charitable_Template' );
        }

        /**
         * Return classes that will be applied to the field.
         *
         * @since  1.5.0
         *
         * @param  array $field Field definition.
         * @param  int   $index Field index.
         * @return string
         */
        public function get_field_classes( $field, $index = 0 ) {
            if ( 'hidden' == $field['type'] ) {
                return;
            }

            $classes = $this->get_field_type_classes( $field['type'] );

            if ( array_key_exists( 'class', $field ) ) {
                $classes[] = $field['class'];
            }

            if ( array_key_exists( 'required', $field ) && $field['required'] ) {
                $classes[] = 'required-field';
            }

            if ( array_key_exists( 'fullwidth', $field ) && $field['fullwidth'] ) {
                $classes[] = 'fullwidth';
            } elseif ( $index > 0 ) {
                $classes[] = $index % 2 ? 'odd' : 'even';
            }

            /**
             * Filter the array of classes before it is returned as a string.
             *
             * @since 1.0.0
             *
             * @param array $classes List of classes.
             * @param array $field   Field definition.
             * @param int   $index   The field index.
             */
            $classes = apply_filters( 'charitable_form_field_classes', $classes, $field, $index );

            return implode( ' ', $classes );
        }

        /**
         * Return array of classes based on the field type.
         *
         * @since  1.5.0
         *
         * @param  string $type Type of field.
         * @return string[]
         */
        public function get_field_type_classes( $type ) {
            $classes = array();

            switch ( $type ) {
                case 'paragraph' :
                    $classes[] = 'charitable-form-content';
                    break;

                case 'fieldset' :
                    $classes[] = 'charitable-fieldset';
                    break;

                default :
                    $classes[] = 'charitable-form-field';
                    $classes[] = 'charitable-form-field-' . $type;
            }

            return $classes;
        }
    }

endif; // End interface_exists check.