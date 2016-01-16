<?php

namespace maru;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use ifteam\SuperUser\SuperUser;
use pocketmine\IPlayer;
use pocketmine\event\player\PlayerKickEvent;

class MySeat extends PluginBase implements Listener {
	/**
	 *
	 * @var SuperUser
	 */
	private $su;
	private $suList = [ ];
	public function onEnable() {
		$this->su = $this->getServer ()->getPluginManager ()->getPlugin ( "SuperUser" );
		if ($this->su === null) {
			$this->getLogger ()->error ( "SuperUser 플러그인을 찾을 수 없습니다." );
			$this->getServer ()->getPluginManager ()->disablePlugin ( $this );
			return;
		}
		$this->getServer ()->getPluginManager ()->registerEvents ( $this, $this );
	}
	public function onPlayerKickEvent(PlayerKickEvent $event) {
		if ($event->getReason () == "disconnectionScreen.serverFull") {
			if (count ( $this->suList ) === 0)
				$this->getSuList ();
			if ($this->isManager ( $event->getPlayer () ))
				$event->setCancelled ();
		}
	}
	private function getSuList() {
		if (isset ( $this->su->db ['su'] ))
			foreach ( $this->su->db ['su'] as $passkey => $data )
				if ($data ['firstLoginName'] !== null)
					$this->suList [$data ['firstLoginName']] = true;
		if (isset ( $this->su->db ['staff'] ))
			foreach ( $staff as $passkey => $data )
				$this->suList [$data ['firstLoginName']] = true;
	}
	/**
	 *
	 * @param IPlayer|string $player        	
	 * @return boolean
	 */
	private function isManager($player) {
		if (! $player instanceof IPlayer)
			return false;
		
		if (isset ( $this->suList [$player->getName ()] ))
			return true;
		
		return false;
	}
}