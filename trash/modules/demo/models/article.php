<?php

class article_model extends model_model
{
    
    function get_articles()
    {
        $this->db->select("a.*, au.firstname, au.lastname");
        $this->db->from("demo_articles a");
        $this->db->join("demo_authors au", "au.author_id=a.author_id", "LEFT");
		$this->db->where('public', 'y');
		$this->db->orderby('article_id', 'DESC');
		$this->db->get();
        return $this->db->result_array();
    }
	
    function get_article($id)
    {
        $this->db->select("a.*, au.firstname, au.lastname");
        $this->db->from("demo_articles a");
        $this->db->join("demo_authors au", "au.author_id=a.author_id", "LEFT");
		$this->db->where('article_id', (int)$id);
		$this->db->get();

        return $this->db->row_array();
    }
	
	function count_articles()
	{
		$this->db->query('select COUNT(*) as tot from demo_articles');
		return $this->db->row_object()->tot;
	}
}

