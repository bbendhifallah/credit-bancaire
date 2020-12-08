<?php

namespace App\Entity;

/**
 * Reimbursement
 */
class Reimbursement
{
    public $date;
    public $due;
    public $interest;
    public $reimbursed;

    public function __construct($data)
    {
        $this->date = $data['date'];
        $this->due = $data['due'];
        $this->interest = $data['interest'];
        $this->reimbursed = $data['reimbursed'];
    }
}