<?php

namespace App\Modules;

use PDO;

class Paginator
{
    
    public $offset;
    public $limit;
    public $data;
    public $previous = '';
    public $next = '';
    public $paginationLinks = '';
    Public $totalPages;

    public function __construct($get, $rowPerPage, $connection, $table)
    {
        $db = $connection;

        $page = $this->validatePage($get);

        $this->limit = $rowPerPage;

        $this->offset = $rowPerPage * ($page - 1);

        $this->totalPages = ceil($this->getCountOffAllRows($db, $table) / $this->limit);

        $stmt = $db->prepare("SELECT * FROM $table LIMIT :limit OFFSET :offset");

        $stmt->bindValue(':limit', $this->limit, PDO::PARAM_INT);

        $stmt->bindValue(':offset', $this->offset, PDO::PARAM_INT);
        
        $stmt->execute();

        $this->createPaginationLinks();

        $this->data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function getCountOffAllRows($db, $table)
    {
        $stmt = $db->query("SELECT count(*) FROM $table");
        return $stmt->fetchColumn();
    }

    protected function validatePage($get)
    {   
        $page = isset($get['page'])  && filter_var($get['page'], FILTER_VALIDATE_INT) ? abs($get['page']) : 1;
        return $page;
    }

    protected function createPaginationLinks()
    {
        $baseUrl = explode('?', $_SERVER['REQUEST_URI'])[0];

        $page = $_GET['page'] ?? 1;
        $page = abs(filter_var($page, FILTER_VALIDATE_INT, ['default' => 1]));

        if($page > 1){
            $numberForPrevious = $page - 1;
            $this->previous = "<a href='$baseUrl?page=$numberForPrevious'>Previous</a>";
        }

        if( $this->totalPages >= $page +1){
            $numberForPrevious = $page + 1;
            $this->next = "<a href='$baseUrl?page=$numberForPrevious'>Next</a>";
        }

        for ($i=1; $i <= $this->totalPages; $i++) { 
            $this->paginationLinks .= "<a href='$baseUrl?page=$i'>$i</a>";
        }
        $this->paginationLinks = $this->previous.$this->paginationLinks.$this->next;
    }

}