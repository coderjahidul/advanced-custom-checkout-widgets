/**
* Elementor Advanced Checkout Widget
*
* Custom Elementor widget that renders the WooCommerce checkout form
* with additional product selection features.
*/
class Elementor_Advanced_Checkout_Widget extends \Elementor\Widget_Base
{

/**
* Get widget name
*
* @return string
*/
public function get_name()
{
return 'adv_checkout';
}

/**
* Get widget title
*
* @return string
*/
public function get_title()
{
return esc_html__('Advanced Checkout', 'advanced-checkout');
}

public function get_icon()
{
return 'eicon-checkout';
}

public function get_categories()
{
return ['advanced-checkout'];
}

public function get_style_depends()
{
return ['advanced-checkout-style'];
}

/**
* Register widget controls
*/
protected function register_controls()
{
$this->start_controls_section(
'content_section',
[
'label' => esc_html__('Product Selection', 'advanced-checkout'),
'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
]
);

$this->add_control(
'products_list',
[
'label' => esc_html__('Select Products to Show (Comma separated IDs)', 'advanced-checkout'),
'type' => \Elementor\Controls_Manager::TEXT,
'default' => '',
'description' => esc_html__('Products shown for selection. Leave empty to hide product selection.',
'advanced-checkout'),
]
);

$this->add_control(
'preselected_products_list',
[
'label' => esc_html__('Pre-selected Products (Comma separated IDs)', 'advanced-checkout'),
'type' => \Elementor\Controls_Manager::TEXT,
'default' => '',
'description' => esc_html__('Products automatically added to cart on page load.', 'advanced-checkout'),
]
);

$this->end_controls_section();

// Style Settings
$this->start_controls_section(
'style_section',
[
'label' => esc_html__('Design Settings', 'advanced-checkout'),
'tab' => \Elementor\Controls_Manager::TAB_STYLE,
]
);

$this->add_control(
'heading_color',
[
'label' => esc_html__('Section Headings Color', 'advanced-checkout'),
'type' => \Elementor\Controls_Manager::COLOR,
'selectors' => [
'{{WRAPPER}} .adv-checkout-col-1 h3, {{WRAPPER}} .adv-checkout-col-2 .adv-sticky-sidebar h3#order_review_heading' =>
'color: {{VALUE}};',
],
]
);

$this->add_control(
'button_bg_color',
[
'label' => esc_html__('Button Background Color', 'advanced-checkout'),
'type' => \Elementor\Controls_Manager::COLOR,
'selectors' => [
'{{WRAPPER}} .adv-checkout-col-2 button#place_order' => 'background-color: {{VALUE}};',
],
]
);

$this->add_control(
'button_hover_bg_color',
[
'label' => esc_html__('Button Hover Background Color', 'advanced-checkout'),
'type' => \Elementor\Controls_Manager::COLOR,
'selectors' => [
'{{WRAPPER}} .adv-checkout-col-2 button#place_order:hover' => 'background-color: {{VALUE}};',
],
]
);

$this->add_control(
'button_text_color',
[
'label' => esc_html__('Button Text Color', 'advanced-checkout'),
'type' => \Elementor\Controls_Manager::COLOR,
'selectors' => [
'{{WRAPPER}} .adv-checkout-col-2 button#place_order' => 'color: {{VALUE}};',
],
]
);

$this->add_control(
'button_border_radius',
[
'label' => esc_html__('Button Border Radius', 'advanced-checkout'),
'type' => \Elementor\Controls_Manager::SLIDER,
'size_units' => ['px', '%'],
'range' => [
'px' => [
'min' => 0,
'max' => 50,
'step' => 1,
],
],
'selectors' => [
'{{WRAPPER}} .adv-checkout-col-2 button#place_order' => 'border-radius: {{SIZE}}{{UNIT}};',
],
]
);

$this->add_control(
'asterisk_color',
[
'label' => esc_html__('Required (*) Indicator Color', 'advanced-checkout'),
'type' => \Elementor\Controls_Manager::COLOR,
'selectors' => [
'{{WRAPPER}} .adv-checkout .form-row label .required' => 'color: {{VALUE}};',
],
]
);

/**
* Render widget output on the frontend
*/
protected function render()
{
$settings = $this->get_settings_for_display();

$product_ids = [];
if (!empty($settings['products_list'])) {
$product_ids = array_map('trim', explode(',', $settings['products_list']));
}

$preselected_product_ids = [];
if (!empty($settings['preselected_products_list'])) {
$preselected_product_ids = array_map('trim', explode(',', $settings['preselected_products_list']));
}

// Output Custom Checkout Form template
include ADVANCED_CHECKOUT_PATH . 'templates/checkout-template.php';
}
}