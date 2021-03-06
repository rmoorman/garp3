<?php
/**
 * @author David Spreekmeester | grrr.nl
 */
class Garp_Model_Db_ClusterRecurringJob extends Model_Base_ClusterRecurringJob {
    public function init() {
        parent::init();

        $this->unregisterObserver('Cachable');
        $this->unregisterObserver('Authorable');
    }


    /**
     * @param Int $serverId Database id of the current server in the cluster
     * @param String $lastCheckIn MySQL datetime that represents the last check-in time of this server
     */
    public function fetchDue($serverId, $lastCheckIn) {
        $select = $this->select()
            ->where(
                '`interval` = "daily" AND '
                .'at <= TIME(NOW()) AND '
                .'('
                    .'last_accepted_at IS NULL OR '
                    .'DATE(last_accepted_at) != DATE(NOW())'
                .')'
            )
            ->orWhere(
                '`interval` = "monthly" AND '
                .'at <= TIME(NOW()) AND '
                .'('
                    .'last_accepted_at IS NULL OR '
                    .'NOT ('
                        .'MONTH(last_accepted_at) = MONTH(NOW()) AND '
                        .'YEAR(last_accepted_at) = YEAR(NOW())'
                    .')'
                .')'
            )
        ;
        return $this->fetchAll($select);
    }
}
