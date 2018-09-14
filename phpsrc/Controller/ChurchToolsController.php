<?php

namespace StevenBuehner\Controller;

class ChurchToolsController {

	protected $client;

	public function __construct(ChurchToolsPasswordClient $client) {
		$this->client = $client;
	}

	public function getCategories() {

		$this->checkLogin();

		$module = ChurchToolsPasswordClient::MODULE_CALENDAR;
		$func   = 'getMasterData';

		$response   = $this->client->sendChurchToolsRequest($module, $func);
		$data       = $response->data;
		$categories = $data->category;

		return $categories;
	}

	/**
	 * @throws \StevenBuehner\Exceptions\InvalidLoginException
	 */
	protected function checkLogin() {

		if (!$this->client->isLoggedIn()) {
			$this->client->login();
		}

	}

	public function getCalendars() {

		$this->checkLogin();
		$response = $this->client->sendChurchToolsRequest(ChurchToolsPasswordClient::MODULE_CALENDAR, 'getCalPerCategory');

		return $response->data;
	}

	/**
	 * @param  array $catIds
	 * @param int    $fromDays
	 * @param int    $toDays
	 * @return \stdClass
	 * @throws \StevenBuehner\Exceptions\InvalidLoginException
	 * @throws \StevenBuehner\Exceptions\RequestErrorException
	 */
	public function getEvents($catIds, $fromDays = 0, $toDays = 7) {
		$this->checkLogin();

		$module = ChurchToolsPasswordClient::MODULE_CALENDAR;
		$func   = 'getCalendarEvents';
		$data   = [
			'category_ids' => $catIds,
			'from'         => $fromDays,
			'to'           => $toDays
		];

		$response = $this->client->sendChurchToolsRequest($module, $func, $data);
		$data     = $response->data;

		foreach ($data as $index => $event) {
			$data[$index] = $this->parseEvent($event);
		}

		return $data;
	}

	protected function parseEvent($eventData) {

		$eventData->startdate = new \DateTime($eventData->startdate);
		$eventData->enddate   = new \DateTime($eventData->enddate);
		unset($eventData->bookings);

		return $eventData;

	}

}