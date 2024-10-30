<?php

namespace Clonable\Views;

interface ViewInterface {
    /**
     * The method that will be used the render the content
     * of the tab page
     * @return mixed
     */
    public function render();

    /**
     * The description at the top of the tab page.
     * Is only used when the render_form function of the Forms trait is invoked.
     * @return void does not return just echos
     */
    public function description();
}