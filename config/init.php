<?php

define("ROOT", dirname(__DIR__));

const APP_NAME = 'App';
const PATH = 'http://app.local';
const DEBUG = 0;

const WWW = ROOT . '/public';
const UPLOADS = ROOT . '/uploads';
const APP = ROOT . '/app';
const CORE = ROOT . '/core';
const HELPERS = ROOT . '/helpers';
const CONFIG = ROOT . '/config';
const VIEWS = APP . '/views';
const VALIDATION_RULES = APP . '/Validation/Rules';

const LAYOUT = 'default';
const ENCODING = 'UTF-8';

const DB = [
    'host' => 'localhost',
    'dbname' => 'app_local',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ],
];

const TABLES_WHITELIST = [
    'users', 'posts'
];