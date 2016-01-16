<?php

namespace maru;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use ifteam\SuperUser\SuperUser;
use pocketmine\Player;
use pocketmine\event\player\PlayerPreLoginEvent;

class MySeat extends PluginBase implements Listener {
	/**
	 *
	 * @var SuperUser
	 */
	private $su;
	public function onEnable() {
		$this->su = $this->getServer ()->getPluginManager ()->getPlugin ( "SuperUser" );
		if ($this->su == null) {
			$this->getLogger ()->error ( "SuperUser 플러그인을 찾을 수 없습니다." );
			$this->getServer ()->getPluginManager ()->disablePlugin ( $this );
		}
		$this->getServer ()->getPluginManager ()->registerEvents ( $this, $this );
	}
	public function onPlayerJoin(PlayerPreLoginEvent $event) {
		if ($this->CountOnlinePlayers () >= $this->getServer ()->getMaxPlayers () - $this->CountManagers ()) {
			$player = $event->getPlayer ();
			if (! $this->isManager ( $player )) {
				$event->setKickMessage ( "서버가 꽉 찼습니다!" );
				$event->setCancelled ();
			}
		}
	}
	private function CountManagers() {
		$count = 0;
		foreach ( $this->su->db as $v ) {
			$count ++;
		}
		return $count;
	}
	private function CountOnlinePlayers() {
		$count = 0;
		foreach ( $this->getServer ()->getOnlinePlayers () as $player ) {
			$count ++;
		}
		return $count;
	}
	/**
	 *
	 * @param Player|string $player        	
	 * @return boolean
	 */
	private function isManager($player) {
		if ($player instanceof Player) {
			$player = $player->getName ();
		}
		foreach ( $this->su->db as $su ) {
			foreach ( $su as $pass ) {
				foreach ( $pass as $name ) {
					if ($name == $player)
						return true;
				}
			}
		}
		return false;
	}
}