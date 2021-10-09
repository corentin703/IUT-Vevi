<?php

namespace Model\Gateway;

interface GatewayInterface
{
    public function insert() : void;
    public function update() : void;
    public function delete() : void;
}