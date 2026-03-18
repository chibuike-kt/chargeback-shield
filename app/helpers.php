<?php

if (!function_exists('route_if_exists')) {
  function route_if_exists(string $name, array $params = []): string
  {
    try {
      return route($name, $params);
    } catch (\Exception $e) {
      return '#';
    }
  }
}
