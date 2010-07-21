<?php

echo $this->args['before_widget'];

echo $this->args['before_title'] . $this->title . $this->args['after_title'];

$this->form->render();

echo $this->args['after_widget'];

