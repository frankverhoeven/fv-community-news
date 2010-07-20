<?php

echo $this->args['before_widget'];

echo $this->args['before_title'] . $this->title . $this->args['after_title'];

$this->list->render();

echo $this->args['after_widget'];

