<?php
if(class_exists('Listo_Maintenance_Factory')) {
	class Listo_Maintenance_Mayfly extends Listo_Maintenance_Factory {
		public static function deleteExpired() {
			return Listo_Mayfly::clr();
		}
	}
}

// EOF