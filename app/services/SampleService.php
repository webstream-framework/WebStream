<?php
class SampleService extends CoreService {
    public function model1() {
        return $this->Sample->model1();
    }
    
    public function model2() {
        return $this->Sample->userName();
    }
}
