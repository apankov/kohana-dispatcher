<?php
/**
 * Lightweight events observers
 */
class Events_Dispatcher {

	/**
	 * The stack of events and it's handlers
	 * @var array
	 */
	protected static $observers = null;

	/**
	 * Dispatches the event with passing the data to the callbacks
	 * @param	string $event	Event name
	 * @param	array $data	Event data
	 * @throws	Event_Exception
	 * @return void
	 */
	public static function fire($event, array $params = array())
	{
		if (is_null(self::$observers))
		{
			self::initialize();
		}
		$callbacks = Arr::get(self::$observers, $event, array());
		foreach ($callbacks as $callback)
		{
			if (!is_callable($callback))
			{
				throw new Dispatcher_Exception('Event :event has invalid callback :callback',
					array(':event' => $event, ':callback' => print_r($callback, true)));
			}
			call_user_func_array($callback, $params);
		}
	}

	/**
	 * Registers the callback to the event.
	 * @param	string $event
	 * @param	callback $callback
	 * @return	void
	 */
	public static function register($event, $callback)
	{
		$callbacks = Arr::get(self::$observers, $event, array());
		array_push($callbacks, $callback);
		self::$observers[$event] = $callbacks;
	}

	/**
	 * Initializes observers from the configuration file.
	 */
	public static function initialize()
	{
		self::$observers = array();
		$data = Kohana::$config->load('dispatcher');
		foreach ($data['observers'] as $event => $observers)
		{
			foreach ($observers as $observer)
			{
				self::register($event, $observer);
			}
		}
	}
}
