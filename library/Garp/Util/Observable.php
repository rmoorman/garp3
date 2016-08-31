<?php
/**
 * Garp_Util_Observable
 * Interface for observables.
 *
 * @package Garp_Util
 * @author  Harmen Janssen <harmen@grrr.nl>
 */
interface Garp_Util_Observable {
    /**
     * Register observer. The observer will then listen to events broadcasted
     * from this class.
     *
     * @param Garp_Util_Observer $observer The observer
     * @param string $name Optional custom name
     * @return Garp_Util_Observable $this
     */
    public function registerObserver(Garp_Util_Observer $observer, $name = false);

    /**
     * Unregister observer. The observer will no longer listen to
     * events broadcasted from this class.
     *
     * @param Garp_Util_Observer|String $name The observer or its name
     * @return Garp_Util_Observable $this
     */
    public function unregisterObserver($name);

    /**
     * Broadcast an event. Observers may implement their reaction however
     * they please. The Observable does not expect a return action.
     * If Observers are allowed to modify variables passed, make sure
     * $args contains references instead of values.
     *
     * @param string $event The event name
     * @param array $args The arguments you wish to pass to the observers
     * @return Garp_Util_Observable $this
     */
    public function notifyObservers($event, array $args = array());
}
