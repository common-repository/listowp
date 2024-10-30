<?php
/*
 * Model for listing items
 */
abstract class Listo_Model_Single {

    public $id;
    public $author;
    public $title;
    public $description;
    public $order;
	public $created;
	public $edited;
	public $closed;

    public $editable_properties;

    public function __construct($id=NULL) {
        global $wpdb;
        $this->wpdb = $wpdb;

        // No ID means we are creating a new item
        if(NULL === $id) {
	        $now = current_time('mysql', TRUE);
            $this->wpdb->insert($this->config_table(),['title'=>'','author'=>Listo_User::get_id(),'created'=>$now,'edited'=>$now]);
            $this->id = $this->wpdb->insert_id;
        } else {
            $this->id = $id;
        }

	    if(!defined('LISTOWP_NOPRIV_MODE')) {
		    $query = $this->wpdb->prepare("SELECT * FROM ".$this->config_table()." WHERE id=%d AND author=%d", $this->id, Listo_User::get_id());
	    } else {
		    $query = $this->wpdb->prepare("SELECT * FROM ".$this->config_table()." WHERE id=%d", $this->id);
	    }


	    $result = $this->wpdb->get_row($query, ARRAY_A);

        if(is_array($result)){
            foreach ($result as $k => $v) {
                if (property_exists($this, $k)) {
                    $this->$k = $v;
                }
            }
        } else {
			return FALSE;
        }
    }

    public static function config_table() {
        return NULL;
    }

    public function get_item($return = ARRAY_A) {
        $item = $this;
        unset($item->wpdb);
        unset($item->editable_properties);

        return ($return == ARRAY_A) ? (array) $item : $item;
    }

    public function set($property, $value) {

	    if(!defined('LISTOWP_NOPRIV_MODE')) {
		    if ( $this->author != Listo_User::get_id() ) {
			    die( 'Invalid author' );
		    }
	    }

	    if(property_exists($this, $property)) {

            $validation = new Listo_Value_Validation();
            $value = $validation->_($property,$value);


            if(!$value instanceof Listo_Value_Validation_Error) {
                $this->$property = $value;

	            $set = [$property => $value,'edited'=>current_time('mysql', TRUE)];
				if($property=='order') {
					$set = [$property => $value];
				}

                $this->wpdb->update($this->config_table(), $set, ['id' => $this->id]);
            } else {
                // Listo_Value_Validation_Error...
	            new Listo_Debug('Invalid data: '.print_r($value,true));
                die('Invalid data');
            }
        } else {
            die('Invalid property');
        }
    }

	public function delete() {
		global $wpdb;

		if(!defined('LISTOWP_NOPRIV_MODE')) {
			$sql = $wpdb->prepare( 'DELETE FROM ' . $this->config_table() . ' WHERE id=%d AND author=%d', $this->id, Listo_User::get_id() );
		} else {
			$sql = $wpdb->prepare( 'DELETE FROM ' . $this->config_table() . ' WHERE id=%d', $this->id );
		}

		$wpdb->query($sql);
		return TRUE;
	}
}