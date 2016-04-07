<?php 

class DB {

	/**
	* Return db
	* @var object
	*/
    public $db;
	/**
	* Results limit.
	* @var integer
	*/
    public $limit = 5;

	
	public function __construct($config){

		$this->connect($config);	
	}

	private function connect($config){
            try
            {
                if ( !class_exists('Mongo'))
                {
                    echo ("The MongoDB PECL extension has not been installed or enabled");
                    return false;
                }
                    //$connection=  new \MongoClient($config['connection_string'],array('username'=>$config['username'],'password'=>$config['password']));
                    $connection =  new \MongoClient();
                    return $this->db = $connection->selectDB($config['dbname']);
            }
            catch(Exception $e) 
            {
                var_dump($e);
                return false;
            }
	}
	/**
	 * get one document by id
	 * @return array
	 */
	public function getById($id,$collection){
            // Convert strings of right length to MongoID		
            $table = $this->db->selectCollection($collection);
            $cursor  = $table->find(array('_id' => $id));
            $document = $cursor->getNext();

            if (!$document ){
                return false ;
            }

            return $document;
	}
        
        
        public function getAll($collection, $query=array()){
            // Convert strings of right length to MongoID		
            $table = $this->db->selectCollection($collection);
            
            if ($query)
            {
                $cursor  = $table->find($query);
            }
            else
            {
                $cursor  = $table->find();
            }
            
            $all_documents = array();
            
            foreach ($cursor as $doc) 
            {
                $all_documents[] = $doc;               
            }
            
            return $all_documents;
	}
        
        
                
        
        public function checkCollectionExists($name)
        {
            
            #error_log($name);
            $collections = $this->db->getCollectionNames();                        
            
            return in_array($name, $collections);
        }
        
        
        //public function addNewUserCollection($user_name, $type)
        //{
            
        //}
        
        public function getCollectionNew($name)
        //create collection if not exists and returns the collection
        {
            if (!$this->checkCollectionExists($name))
            {
                $this->db->createCollection($name);
            }
            
            return $this->db->$name;
        }


        /**
	 * get all data in collection and paginator
	 *
	 * @return multi array 
	 */
	// public function get($page,$collection){

	// 	$currentPage = $page;
	// 	$articlesPerPage = $this->limit;

	// 	//number of article to skip from beginning
	// 	$skip = ($currentPage - 1) * $articlesPerPage; 

	// 	$table = $this->db->selectCollection($collection);

	// 	$cursor = $table->find();
	// 	//total number of articles in database
	// 	$totalArticles = $cursor->count(); 
	// 	//total number of pages to display
	// 	$totalPages = (int) ceil($totalArticles / $articlesPerPage); 

	// 	$cursor->sort(array('saved_at' => -1))->skip($skip)->limit($articlesPerPage);
	// 	//$cursor = iterator_to_array($cursor);
	// 	$data=array($currentPage,$totalPages,$cursor);

	// 	return $data;
	// }
	/**
	 * Create article
	 * @return boolean
	 */
	public function create($collection, $document){

		$table 	 = $this->db->selectCollection($collection);
		return $result = $table->insert($document);
	}
	/**
	 * delete article via id
	 * @return boolean
	 */
	public function delete($id,$collection){
		// Convert strings of right length to MongoID		
		$table 	 = $this->db->selectCollection($collection);
		$result = $table->remove(array('_id'=>$id));
		if (!$id){
			return false;
		}
		return $result;

	}
	/**
	 * Update article
	 * @return boolean
	 */
	public function update($id,$collection,$document){
		// Convert strings of right length to MongoID
		//if (strlen($id) == 24){
		//	$id = new \MongoId($id);
		//}
		$table 	 = $this->db->selectCollection($collection);
		$result  = $table->update(
				array('_id' => $id), 
				array('$set' => $document),
				array('upsert' => true)
		);
		if (!$id){
			return false;
		}
		return $result;

	}

	public function bulkUpdate($collection, $documents_to_update, $total=true)
	{                   
		$table 	 = $this->db->selectCollection($collection);

		if ($total)
		{

	                $result_update = $table->save($documents_to_update);
                
        	        if (!$result_update)
                	{
	                    return false;
			}
                }
		else
		{                                
			foreach ($documents_to_update as $document)
			{            
                        
				$result_update = $table->save($document);
			}
                        
			if (!$result_update)
			{
				return false;
			}

		}
		return $result_update;
	}
	

}
