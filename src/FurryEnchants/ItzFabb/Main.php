<?php

namespace FurryEnchants\ItzFabb;

//Basic Class 
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as C;

//Enchantments Class
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\{Enchantment, EnchantmentInstance};

//Guis Class 
use pocketmine\scheduler\ClosureTask;
use libs\muqsit\invmenu\InvMenu;
use libs\muqsit\invmenu\InvMenuHandler;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\event\inventory\InventoryTransactionEvent;

//Command Class 
use pocketmine\command\Command;
use pocketmine\command\Commandsender;
use pocketmine\command\ConsoleCommandSender;

//Sound Class 
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

//Others
use onebone\economyapi\EconomyAPI;
use DaPigGuy\PiggyCustomEnchants\CustomEnchants\CustomEnchants;

class Main extends PluginBase implements Listener {
	public function onEnable(){
		if (is_null($this->getServer()->getPluginManager()->getPlugin("EconomyAPI"))) {
            $this->getLogger()->error("§c<Warning> §7You dont have EconomyAPI Installed! plugin is disabling...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
          if (is_null($this->getServer()->getPluginManager()->getPlugin("InvCrashFix"))) {
            $this->getLogger()->error("§c<Warning> §7You dont have InvCrashFix installed! plugin is disabling...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
          @mkdir($this->getDataFolder());
          $this->economyAPI = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
          $this->saveResource("config.yml");
          $this->saveDefaultConfig();
          $this->getServer()->getPluginManager()->registerEvents($this, $this);
          $this->getLogger()->info("______                     _____           _                 _       ");
          $this->getLogger()->info("|  ___|                   |  ___|         | |               | |      ");
          $this->getLogger()->info("| |_ _   _ _ __ _ __ _   _| |__ _ __   ___| |__   __ _ _ __ | |_ ___ ");
          $this->getLogger()->info("|  _| | | | '__| '__| | | |  __| '_ \ / __| '_ \ / _` | '_ \| __/ __|");
          $this->getLogger()->info("| | | |_| | |  | |  | |_| | |__| | | | (__| | | | (_| | | | | |_\__ \ ");
          $this->getLogger()->info("\_|  \__,_|_|  |_|   \__, \____/_| |_|\___|_| |_|\__,_|_| |_|\__|___/");
          $this->getLogger()->info("                      __/ |                                          ");
          $this->getLogger()->info("                     |___/                                         ");  
          $this->getLogger()->info(" §r ");
          $this->getLogger()->info(" §r ");
          $this->getLogger()->info(" §r ");
          $this->getLogger()->info(" §r ");
          $this->getLogger()->info(" §r ");
          $this->getLogger()->info(" §r ");
          $this->getLogger()->info("§aPlugin Enabled!");
          $this->getLogger()->info("§eFurryEnchants by ItzFabb");
          $this->getLogger()->info("§ba enchant shop but in gui's!");
          
          $this->enchantsize = InvMenu::create(InvMenu::TYPE_HOPPER);
          $this->swordsize = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
          if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
	}
     public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        switch($cmd->getName()){
        	case "enchantingtable":
        		if(!$sender instanceof Player){
        			$sender->sendMessage($this->getConfig()->get("command-ingame"));
        			return false;
        		}
             if(!$sender->hasPermission("furryenchants.enchant")){
             	$sender->sendMessage($this->getConfig()->get("no-permission"));
             	$volume = mt_rand();
	          $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
	          return false;
             }
             $this->enchantMenu($sender);
             $sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_CHEST_OPEN);
            return true;
             break;
        }
     }
     public function enchantMenu(Player $sender): void 
     {
	    $this->enchantsize->readonly();
	    $this->enchantsize->setListener([$this, "enchantMenu1"]);
         $this->enchantsize->setName($this->getConfig()->get("title"));
	    $inventory = $this->enchantsize->getInventory();
	    
	    //Chest Section 0-8
	    $inventory->setItem(0, Item::get(276, 0, 1)->setCustomName($this->getConfig()->get("sword")));
	    $inventory->setItem(1, Item::get(160, 15, 1)->setCustomName("§r"));
	    $inventory->setItem(2, Item::get(279, 0, 1)->setCustomName($this->getConfig()->get("tools")));
	    $inventory->setItem(3, Item::get(160, 15, 1)->setCustomName("§r"));
	    $inventory->setItem(4, Item::get(310, 0, 1)->setCustomName($this->getConfig()->get("armor")));
	    $this->enchantsize->send($sender);
	}
	public function enchantMenu1(Player $sender, Item $item)
	{
		$hand = $sender->getInventory()->getItemInHand()->getCustomName();
          $inventory = $this->enchantsize->getInventory();
          
          if($item->getId() == 276){
          	$sender->removeWindow($inventory);
          	$sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_CHEST_CLOSED);
               $seconds = 2; 
               $this->getScheduler()->scheduleDelayedTask(new \pocketmine\scheduler\ClosureTask( 
            	  function(int $currentTick) use ($sender): void {
            		$this->sword($sender);
            		$sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_CHEST_OPEN);
            	}
            	), 5 * $seconds);
          }
          if($item->getId() == 279){
          	$sender->removeWindow($inventory);
          	$sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_CHEST_CLOSED);
               $seconds = 2; 
               $this->getScheduler()->scheduleDelayedTask(new \pocketmine\scheduler\ClosureTask( 
            	  function(int $currentTick) use ($sender): void {
            		$this->tools($sender);
            		$sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_CHEST_OPEN);
            	}
            	), 4 * $seconds);
          }
          if($item->getId() == 310){
          	$sender->removeWindow($inventory);
          	$sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_CHEST_CLOSED);
               $seconds = 2; 
               $this->getScheduler()->scheduleDelayedTask(new \pocketmine\scheduler\ClosureTask( 
            	  function(int $currentTick) use ($sender): void {
            		$this->armor($sender);
            		$sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_CHEST_OPEN);
            	}
            	), 4 * $seconds);
          }
          if($item->getId() == 160 && $item->getDamage() == 15){
          	$volume = mt_rand();
	          $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_CLICK, (int) $volume);
          }
	}
	public function sword($sender){
	     $this->swordsize->readonly();
	     $this->swordsize->setListener([$this, "sword1"]);
          $this->swordsize->setName($this->getConfig()->get("swordtitle"));
	     $inventory = $this->swordsize->getInventory();
	     
	     //Chest Section 1-8
	     $inventory->setItem(0, Item::get(160, 15, 1)->setCustomName("§r"));
	     $inventory->setItem(1, Item::get(160, 15, 1)->setCustomName("§r"));
	     $inventory->setItem(2, Item::get(160, 15, 1)->setCustomName("§r"));
	     $inventory->setItem(3, Item::get(160, 15, 1)->setCustomName("§r"));
	     $inventory->setItem(4, Item::get(339, 0, 1)->setCustomName("§r§b§lSword Enchant Page\n\n§r§7In here you could enchant your sword\n§7to increase the damage, durability and etc!"));
	     $inventory->setItem(5, Item::get(160, 15, 1)->setCustomName("§r"));
	     $inventory->setItem(6, Item::get(160, 15, 1)->setCustomName("§r"));
	     $inventory->setItem(7, Item::get(160, 15, 1)->setCustomName("§r"));
	     $inventory->setItem(8, Item::get(160, 15, 1)->setCustomName("§r"));
	    //Chest Section 9-17 #Sharpness
	    $inventory->setItem(9, Item::get(340, 0, 1)->setCustomName("§r§b§lSharpness Enchantment§r §8(I-VII)\n\n§7Enchantment: Sharpness\n§r§8Increase your sword damage per level\n§8to the undead mob or player!"));
	    $inventory->setItem(10, Item::get(387, 0, 1)->setCustomName("§r§b§lSharpness I\n\n§r§8Increase your weapons damage by 1 level!\n§8to the undead mob! or player\n\n\n§7Cost: §a§l⛃§r§a1.000\n§r§7Lapis Lazuli: §c2"));
	    $inventory->setItem(11, Item::get(387, 0, 1)->setCustomName("§r§b§lSharpness II\n\n§r§8Increase your weapons damage by 2 level!\n§8to the undead mob! or player\n\n\n§7Cost: §a§l⛃§r§a2.000\n§r§7Lapis Lazuli: §c4"));
	    $inventory->setItem(12, Item::get(387, 0, 1)->setCustomName("§r§b§lSharpness III\n\n§r§8Increase your weapons damage by 3 level!\n§8to the undead mob! or player\n\n\n§7Cost: §a§l⛃§r§a3.500\n§r§7Lapis Lazuli: §c8"));
	    $inventory->setItem(13, Item::get(387, 0, 1)->setCustomName("§r§b§lSharpness IV\n\n§r§8Increase your weapons damage by 4 level!\n§8to the undead mob! or player\n\n\n§7Cost: §a§l⛃§r§a5.000\n§r§7Lapis Lazuli: §c12"));
	    $inventory->setItem(14, Item::get(387, 0, 1)->setCustomName("§r§b§lSharpness V\n\n§r§8Increase your weapons damage by 5 level!\n§8to the undead mob! or player\n\n\n§7Cost: §a§l⛃§r§a7.500\n§r§7Lapis Lazuli: §c20"));
	    $inventory->setItem(15, Item::get(387, 0, 1)->setCustomName("§r§b§lSharpness VI\n\n§r§8Increase your weapons damage by 6 level!\n§8to the undead mob! or player\n\n\n§7Cost: §a§l⛃§r§a10.000\n§r§7Lapis Lazuli: §c25"));
	    $inventory->setItem(16, Item::get(387, 0, 1)->setCustomName("§r§b§lSharpness VII\n\n§r§8Increase your weapons damage by 7 level!\n§8to the undead mob! or player\n\n\n§7Cost: §a§l⛃§r§a15.000\n§r§7Lapis Lazuli: §c50"));
	    $inventory->setItem(17, Item::get(160, 15, 1)->setCustomName("§r"));
	    //Chest Section 18-26
	    $inventory->setItem(18, Item::get(340, 0, 1)->setCustomName("§r§b§lSmite Enchantment§r §8(I-VII)\n\n§7Enchantment: Smite\n§r§8Increase your sword damage per level\n§8to the undead mob and not to player!"));
	    $inventory->setItem(19, Item::get(387, 0, 1)->setCustomName("§r§b§lSmite I\n\n§r§8Increase your weapons damage by 1 level!\n§8to the undead mob not the player\n\n\n§7Cost: §a§l⛃§r§a750\n§r§7Lapis Lazuli: §c2"));
	    $inventory->setItem(20, Item::get(387, 0, 1)->setCustomName("§r§b§lSmite II\n\n§r§8Increase your weapons damage by 2 level!\n§8to the undead mob not the player\n\n\n§7Cost: §a§l⛃§r§a1.500\n§r§7Lapis Lazuli: §c5"));
	    $inventory->setItem(21, Item::get(387, 0, 1)->setCustomName("§r§b§lSmite III\n\n§r§8Increase your weapons damage by 3 level!\n§8to the undead mob not the player\n\n\n§7Cost: §a§l⛃§r§a2.750\n§r§7Lapis Lazuli: §c9"));
	    $inventory->setItem(22, Item::get(387, 0, 1)->setCustomName("§r§b§lSmite IV\n\n§r§8Increase your weapons damage by 4 level!\n§8to the undead mob not the player\n\n\n§7Cost: §a§l⛃§r§a4.500\n§r§7Lapis Lazuli: §c15"));
	    $inventory->setItem(23, Item::get(387, 0, 1)->setCustomName("§r§b§lSmite V\n\n§r§8Increase your weapons damage by 5 level!\n§8to the undead mob not the player\n\n\n§7Cost: §a§l⛃§r§a6.500\n§r§7Lapis Lazuli: §c21"));
	    $inventory->setItem(24, Item::get(387, 0, 1)->setCustomName("§r§b§lSmite VI\n\n§r§8Increase your weapons damage by 6 level!\n§8to the undead mob not the player\n\n\n§7Cost: §a§l⛃§r§a7.750\n§r§7Lapis Lazuli: §c29"));
	    $inventory->setItem(25, Item::get(387, 0, 1)->setCustomName("§r§b§lSmite VII\n\n§r§8Increase your weapons damage by 7 level!\n§8to the undead mob not the player\n\n\n§7Cost: §a§l⛃§r§a10.250\n§r§7Lapis Lazuli: §c38"));
	    $inventory->setItem(26, Item::get(160, 14, 1)->setCustomName("§r§c§lEXIT\n\n§r§7Click to exit the menu!"));
	    //Chest Section 27-35
	    $inventory->setItem(27, Item::get(340, 0, 1)->setCustomName("§r§b§lLooting Enchantment§r §8(I-VII)\n\n§7Enchantment: Looting\n§r§8Increase your mob drops per level\n§8to get more items from the mobs you killed!"));
	    $inventory->setItem(28, Item::get(387, 0, 1)->setCustomName("§r§b§lLooting I\n\n§r§8Increase your mob drops by 1 level!\n§8to get more items from the mob you killed!\n\n\n§7Cost: §a§l⛃§r§a1.000\n§r§7Lapis Lazuli: §c5"));
	    $inventory->setItem(29, Item::get(387, 0, 1)->setCustomName("§r§b§lLooting II\n\n§r§8Increase your mob drops by 2 level!\n§8to get more items from the mob you killed!\n\n\n§7Cost: §a§l⛃§r§a2.500\n§r§7Lapis Lazuli: §c10"));
	    $inventory->setItem(30, Item::get(387, 0, 1)->setCustomName("§r§b§lLooting III\n\n§r§8Increase your mob drops by 3 level!\n§8to get more items from the mob you killed!\n\n\n§7Cost: §a§l⛃§r§a5.000\n§r§7Lapis Lazuli: §c15"));
	    $inventory->setItem(31, Item::get(387, 0, 1)->setCustomName("§r§b§lLooting IV\n\n§r§8Increase your mob drops by 4 level!\n§8to get more items from the mob you killed!\n\n\n§7Cost: §a§l⛃§r§a7.500\n§r§7Lapis Lazuli: §c20"));
	    $inventory->setItem(32, Item::get(387, 0, 1)->setCustomName("§r§b§lLooting V\n\n§r§8Increase your mob drops by 5 level!\n§8to get more items from the mob you killed!\n\n\n§7Cost: §a§l⛃§r§a10.000\n§r§7Lapis Lazuli: §c25"));
	    $inventory->setItem(33, Item::get(387, 0, 1)->setCustomName("§r§b§lLooting VI\n\n§r§8Increase your mob drops by 6 level!\n§8to get more items from the mob you killed!\n\n\n§7Cost: §a§l⛃§r§a15.000\n§r§7Lapis Lazuli: §c30"));
	    $inventory->setItem(34, Item::get(387, 0, 1)->setCustomName("§r§b§lLooting VII\n\n§r§8Increase your mob drops by 7 level!\n§8to get more items from the mob you killed!\n\n\n§7Cost: §a§l⛃§r§a20.000\n§r§7Lapis Lazuli: §c35"));
	    $inventory->setItem(35, Item::get(399, 0, 1)->setCustomName("§r§d§lMAIN MENU\n\n§r§7Click to go back to main menu"));
	    //Chest Section 36-44
	    $inventory->setItem(36, Item::get(340, 0, 1)->setCustomName("§r§b§lFire Aspect Enchantment§r §8(I-VII)\n\n§7Enchantment: Fire Aspect\n§r§8Set your target or enemy on fire!\n§8plus increase the sword damage per level!"));
	    $inventory->setItem(37, Item::get(387, 0, 1)->setCustomName("§r§b§lFire Aspect I\n\n§r§8Set your enemy on fire!\n§8and increase the sword fire damage by 1 level!\n\n\n§7Cost: §a§l⛃§r§a1.000\n§r§7Lapis Lazuli: §c8"));
	    $inventory->setItem(38, Item::get(387, 0, 1)->setCustomName("§r§b§lFire Aspect II\n\n§r§8Set your enemy on fire!\n§8and increase the sword fire damage by 2 level!\n\n\n§7Cost: §a§l⛃§r§a2.000\n§r§7Lapis Lazuli: §c12"));
	    $inventory->setItem(39, Item::get(387, 0, 1)->setCustomName("§r§b§lFire Aspect III\n\n§r§8Set your enemy on fire!\n§8and increase the sword fire damage by 3 level!\n\n\n§7Cost: §a§l⛃§r§a3.000\n§r§7Lapis Lazuli: §c16"));
	    $inventory->setItem(40, Item::get(387, 0, 1)->setCustomName("§r§b§lFire Aspect IV\n\n§r§8Set your enemy on fire!\n§8and increase the sword fire damage by 4 level!\n\n\n§7Cost: §a§l⛃§r§a4.000\n§r§7Lapis Lazuli: §c22"));
	    $inventory->setItem(41, Item::get(387, 0, 1)->setCustomName("§r§b§lFire Aspect V\n\n§r§8Set your enemy on fire!\n§8and increase the sword fire damage by 5 level!\n\n\n§7Cost: §a§l⛃§r§a5.000\n§r§7Lapis Lazuli: §c26"));
	    $inventory->setItem(42, Item::get(387, 0, 1)->setCustomName("§r§b§lFire Aspect VI\n\n§r§8Set your enemy on fire!\n§8and increase the sword fire damage by 6 level!\n\n\n§7Cost: §a§l⛃§r§a6.000\n§r§7Lapis Lazuli: §c32"));
	    $inventory->setItem(43, Item::get(387, 0, 1)->setCustomName("§r§b§lFire Aspect VII\n\n§r§8Set your enemy on fire!\n§8and increase the sword fire damage by 7 level!\n\n\n§7Cost: §a§l⛃§r§a7.000\n§r§7Lapis Lazuli: §c40"));
	    $inventory->setItem(44, Item::get(160, 15, 1)->setCustomName("§r"));
	    //Chest Section 45-53
	    $inventory->setItem(45, Item::get(340, 0, 1)->setCustomName("§r§b§lKnockback Enchantment§r §8(I-VII)\n\n§7Enchantment: Knockback\n§r§8Increase your sword Knockback!\n§8to make enemy hard to get kill!"));
	    $inventory->setItem(46, Item::get(387, 0, 1)->setCustomName("§r§b§lKnocback I\n\n§r§8Increase your sword knockback by 1 level!\n§8to knock your enemy far away!\n\n\n§7Cost: §a§l⛃§r§a500\n§r§7Lapis Lazuli: §c4"));
	    $inventory->setItem(47, Item::get(387, 0, 1)->setCustomName("§r§b§lKnocback II\n\n§r§8Increase your sword knockback by 2 level!\n§8to knock your enemy far away!\n\n\n§7Cost: §a§l⛃§r§a750\n§r§7Lapis Lazuli: §c7"));
	    $inventory->setItem(48, Item::get(387, 0, 1)->setCustomName("§r§b§lKnocback III\n\n§r§8Increase your sword knockback by 3 level!\n§8to knock your enemy far away!\n\n\n§7Cost: §a§l⛃§r§a1.000\n§r§7Lapis Lazuli: §c10"));
	    $inventory->setItem(49, Item::get(387, 0, 1)->setCustomName("§r§b§lKnocback IV\n\n§r§8Increase your sword knockback by 4 level!\n§8to knock your enemy far away!\n\n\n§7Cost: §a§l⛃§r§a1.500\n§r§7Lapis Lazuli: §c13"));
	    $inventory->setItem(50, Item::get(387, 0, 1)->setCustomName("§r§b§lKnocback V\n\n§r§8Increase your sword knockback by 5 level!\n§8to knock your enemy far away!\n\n\n§7Cost: §a§l⛃§r§a2.000\n§r§7Lapis Lazuli: §c17"));
	    $inventory->setItem(51, Item::get(387, 0, 1)->setCustomName("§r§b§lKnocback VI\n\n§r§8Increase your sword knockback by 6 level!\n§8to knock your enemy far away!\n\n\n§7Cost: §a§l⛃§r§a2.500\n§r§7Lapis Lazuli: §c20"));
	    $inventory->setItem(52, Item::get(387, 0, 1)->setCustomName("§r§b§lKnocback VII\n\n§r§8Increase your sword knockback by 7 level!\n§8to knock your enemy far away!\n\n\n§7Cost: §a§l⛃§r§a3.500\n§r§7Lapis Lazuli: §c23"));
	    $inventory->setItem(53, Item::get(160, 15, 1)->setCustomName("§r"));
	    $this->swordsize->send($sender);
	}
	public function sword1(Player $sender, Item $item){
		$hand = $sender->getInventory()->getItemInHand()->getCustomName();
          $inventory = $this->swordsize->getInventory();
          
          if($item->getCustomName() === "§r§b§lSharpness I\n\n§r§8Increase your weapons damage by 1 level!\n§8to the undead mob! or player\n\n\n§7Cost: §a§l⛃§r§a1.000\n§r§7Lapis Lazuli: §c2"){
               $money = $this->economyAPI->myMoney($sender);
          	$inv = $sender->getInventory();
          	$item = Item::get(351, 4, 2);
             if($inv->contains($item)){
             	 $inv->removeItem(Item::get(351, 4, 2));
             } else {
             $sender->sendMessage("§r§c§l> §r§7You don't have enough §9lapis lazuli§7 to buy this enchant!");
	        $volume = mt_rand();
	        $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
             }
	        if($money >= 1000){
	          $this->economyAPI->reduceMoney($sender, 1000);
		     $sender->sendMessage("§r§a§l> §r§7You have bought §aSharpness I §7enchantment!");
		     $volume = mt_rand();
	          $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ORB, (int) $volume);
		}else{
	        $sender->sendMessage("§r§c§l> §r§7You don't have enough money to buy this enchant!");
	        $volume = mt_rand();
	        $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
		}
		$sender->removeWindow($inventory);
          $sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_BLOCK_SMITHING_TABLE_USE);
          $this->getServer()->dispatchCommand($sender, "enchant ".$sender->getName()." sharpness 1");
          }
          if($item->getCustomName() === "§r§b§lSharpness II\n\n§r§8Increase your weapons damage by 2 level!\n§8to the undead mob! or player\n\n\n§7Cost: §a§l⛃§r§a2.000\n§r§7Lapis Lazuli: §c4"){
            	$inv = $sender->getInventory();
          	$item = Item::get(351, 4, 4);
             if($inv->contains($item)){
             	$inv->removeItem(Item::get(351, 4, 4));
             } else {
             $sender->sendMessage("§r§c§l> §r§7You don't have enough §9lapis lazuli§7 to buy this enchant!");
	        $volume = mt_rand();
	        $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
             }
	        if($money >= 2000){
	          $this->economyAPI->reduceMoney($sender, 2000); 
		     $sender->sendMessage("§r§a§l> §r§7You have bought §aSharpness II §7enchantment!");
		     $volume = mt_rand();
	          $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ORB, (int) $volume);
		}else{
	        $sender->sendMessage("§r§c§l> §r§7You don't have enough money to buy this enchant!");
	        $volume = mt_rand();
	        $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
		}
             $sender->removeWindow($inventory);
             $sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_BLOCK_SMITHING_TABLE_USE);
             $this->getServer()->dispatchCommand($sender, "enchant ".$sender->getName()." sharpness 2");
          }
          if($item->getCustomName() === "§r§b§lSharpness III\n\n§r§8Increase your weapons damage by 3 level!\n§8to the undead mob! or player\n\n\n§7Cost: §a§l⛃§r§a3.500\n§r§7Lapis Lazuli: §c8"){
          	$inv = $sender->getInventory();
          	$item = Item::get(351, 4, 8);
             if($inv->contains($item)){
             	$inv->removeItem(Item::get(351, 4, 8));
             } else {
             $sender->sendMessage("§r§c§l> §r§7You don't have enough §9lapis lazuli§7 to buy this enchant!");
	        $volume = mt_rand();
	        $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
             }
	        if($money >= 3500){
	          $this->economyAPI->reduceMoney($sender, 3500); 
		     $sender->sendMessage("§r§a§l> §r§7You have bought §aSharpness III §7enchantment!");
		     $volume = mt_rand();
	          $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ORB, (int) $volume);
		}else{
	        $sender->sendMessage("§r§c§l> §r§7You don't have enough money to buy this enchant!");
	        $volume = mt_rand();
	        $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
		}
               $sender->removeWindow($inventory);
          	$sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_BLOCK_SMITHING_TABLE_USE);
          	$this->getServer()->dispatchCommand($sender, "enchant ".$sender->getName()." sharpness 3");
          }
          if($item->getCustomName() === "§r§b§lSharpness IV\n\n§r§8Increase your weapons damage by 4 level!\n§8to the undead mob! or player\n\n\n§7Cost: §a§l⛃§r§a5.000\n§r§7Lapis Lazuli: §c12"){
          	$inv = $sender->getInventory();
          	$item = Item::get(351, 4, 12);
             if($inv->contains($item)){
             	$inv->removeItem(Item::get(351, 4, 12));
             } else {
             $sender->sendMessage("§r§c§l> §r§7You don't have enough §9lapis lazuli§7 to buy this enchant!");
	        $volume = mt_rand();
	        $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
             }
	        if($money >= 5000){
	          $this->economyAPI->reduceMoney($sender, 5000); 
		     $sender->sendMessage("§r§a§l> §r§7You have bought §aSharpness IV §7enchantment!");
		     $volume = mt_rand();
	          $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ORB, (int) $volume);
		}else{
	        $sender->sendMessage("§r§c§l> §r§7You don't have enough money to buy this enchant!");
	        $volume = mt_rand();
	        $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
		}
               $sender->removeWindow($inventory);
          	$sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_BLOCK_SMITHING_TABLE_USE);
          	$this->getServer()->dispatchCommand($sender, "enchant ".$sender->getName()." sharpness 4");
          }
          if($item->getCustomName() === "§r§b§lSharpness V\n\n§r§8Increase your weapons damage by 5 level!\n§8to the undead mob! or player\n\n\n§7Cost: §a§l⛃§r§a7.500\n§r§7Lapis Lazuli: §c20"){ 
          	$inv = $sender->getInventory();
          	$item = Item::get(351, 4, 20);
             if($inv->contains($item)){
             	$inv->removeItem(Item::get(351, 4, 20));
             } else {
             $sender->sendMessage("§r§c§l> §r§7You don't have enough §9lapis lazuli§7 to buy this enchant!");
	        $volume = mt_rand();
	        $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
             }
	        if($money >= 7500){
	          $this->economyAPI->reduceMoney($sender, 7500); 
		     $sender->sendMessage("§r§a§l> §r§7You have bought §aSharpness V §7enchantment!");
		     $volume = mt_rand();
	          $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ORB, (int) $volume);
		}else{
	        $sender->sendMessage("§r§c§l> §r§7You don't have enough money to buy this enchant!");
	        $volume = mt_rand();
	        $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
		}
               $sender->removeWindow($inventory);
          	$sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_BLOCK_SMITHING_TABLE_USE);
          	$this->getServer()->dispatchCommand($sender, "enchant ".$sender->getName()." sharpness 5");
          }
          if($item->getCustomName() === "§r§b§lSharpness VI\n\n§r§8Increase your weapons damage by 6 level!\n§8to the undead mob! or player\n\n\n§7Cost: §a§l⛃§r§a10.000\n§r§7Lapis Lazuli: §c25"){
          	$inv = $sender->getInventory();
          	$item = Item::get(351, 4, 25);
             if($inv->contains($item)){
             	$inv->removeItem(Item::get(351, 4, 25));
             } else {
             $sender->sendMessage("§r§c§l> §r§7You don't have enough §9lapis lazuli§7 to buy this enchant!");
	        $volume = mt_rand();
	        $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
             }
	        if($money >= 10000){
	          $this->economyAPI->reduceMoney($sender, 10000); 
		     $sender->sendMessage("§r§a§l> §r§7You have bought §aSharpness VI §7enchantment!");
		     $volume = mt_rand();
	          $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ORB, (int) $volume);
		}else{
	        $sender->sendMessage("§r§c§l> §r§7You don't have enough money to buy this enchant!");
	        $volume = mt_rand();
	        $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
		}
               $sender->removeWindow($inventory);
          	$sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_BLOCK_SMITHING_TABLE_USE);
          	$this->getServer()->dispatchCommand($sender, "enchant ".$sender->getName()." sharpness 6");
          }
          if($item->getCustomName() === "§r§b§lSharpness VII\n\n§r§8Increase your weapons damage by 7 level!\n§8to the undead mob! or player\n\n\n§7Cost: §a§l⛃§r§a15.000\n§r§7Lapis Lazuli: §c50"){ 
          	$inv = $sender->getInventory();
          	$item = Item::get(351, 4, 50);
             if($inv->contains($item)){
             	$inv->removeItem(Item::get(351, 4, 50));
             } else {
             $sender->sendMessage("§r§c§l> §r§7You don't have enough §9lapis lazuli§7 to buy this enchant!");
	        $volume = mt_rand();
	        $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
             }
	        if($money >= 15000){
	          $this->economyAPI->reduceMoney($sender, 15000); 
		     $sender->sendMessage("§r§a§l> §r§7You have bought §aSharpness VII §7enchantment!");
		     $volume = mt_rand();
	          $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ORB, (int) $volume);
		}else{
	        $sender->sendMessage("§r§c§l> §r§7You don't have enough money to buy this enchant!");
	        $volume = mt_rand();
	        $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
		}
               $sender->removeWindow($inventory);
          	$sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_BLOCK_SMITHING_TABLE_USE);
          	$this->getServer()->dispatchCommand($sender, "enchant ".$sender->getName()." sharpness 7");
          }
	}
}



#############################################################################################################################################################################################################################################################################################################################################################################