<?php
//////////////// Admin Menus -> Pages Callback Functions /////////////////////

function techwatt_dashboard_page() {
    echo '<div class="wrap"><h1>Welcome to Techwatt</h1><p>Use the menu on the left to manage students, LMS, projects, testimonials, payments, and settings.</p></div>';
}

function techwatt_register_students_page() {
    echo '<div class="wrap"><h1>Register Students</h1><p>Here you can manage student registrations.</p></div>';
}

function techwatt_lms_page() {
    echo '<div class="wrap"><h1>LMS</h1><p>Here you can manage the LMS content.</p></div>';
}

function techwatt_projects_page() {
    echo '<div class="wrap"><h1>Projects</h1><p>Here you can manage student projects.</p></div>';
}

function techwatt_testimonials_page() {
    echo '<div class="wrap"><h1>Testimonials</h1><p>Here you can manage testimonials.</p></div>';
}

function techwatt_payments_page() {
    echo '<div class="wrap"><h1>Payments</h1><p>Here you can manage student payments.</p></div>';
}
?>