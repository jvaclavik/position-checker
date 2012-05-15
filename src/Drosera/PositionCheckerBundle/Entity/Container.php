<?php

namespace Drosera\PositionCheckerBundle\Entity;

Class Container{
  public $url;
  public $keyword;
  public $search;
  public $date;
  public $position;
//  public $data;


  
  function __construct($url = null, $search = null, $keyword = null, $position = null) {
    $this->url = $url;
    $this->search = $search;
    $this->keyword = $keyword;
    $this->position = $position;
    $this->date = new \DateTime("now");
    }
  
  public function getUrl() {
    return $this->url;
  }

  public function setUrl($url) {
    $this->url = $url;
  }

  public function getKeyword() {
    return $this->keyword;
  }

  public function setKeyword($keyword) {
    $this->keyword = $keyword;
  }

  public function getSearch() {
    return $this->search;
  }

  public function setSearch($search) {
    $this->search = $search;
  }

  public function getDate() {
    return $this->date;
  }

  public function setDate($date) {
    $this->date = $date;
  }

  public function getPosition() {
    return $this->position;
  }

  public function setPosition($position) {
    $this->position = $position;
  }

//  public function getData() {
//    return $this->data;
//  }
//
//  public function setData($date,$position) {
//    $this->data[$date] = $position;
//  }


  
}

