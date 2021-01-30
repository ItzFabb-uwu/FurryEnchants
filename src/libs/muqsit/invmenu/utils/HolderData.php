<?php

/*

 *  ___            __  __

 * |_ _|_ ____   _|  \/  | ___ _ __  _   _

 *  | || '_ \ \ / / |\/| |/ _ \ '_ \| | | |

 *  | || | | \ V /| |  | |  __/ | | | |_| |

 * |___|_| |_|\_/ |_|  |_|\___|_| |_|\__,_|

 *

 * This program is free software: you can redistribute it and/or modify

 * it under the terms of the GNU Lesser General Public License as published by

 * the Free Software Foundation, either version 3 of the License, or

 * (at your option) any later version.

 *

 * @author Muqsit

 * @link http://github.com/Muqsit

 *

*/

namespace libs\muqsit\invmenu\utils;

use pocketmine\math\Vector3;

class HolderData{

	/** @var Vector3 */

	public $position;

	/** @var string|null */

	public $custom_name;

	public function __construct(Vector3 $position, ?string $custom_name){

		$this->position = $position;

		$this->custom_name = $custom_name;

	}

}#

  name:

    Material: CUSTOM_HEAD

    Custom-mode-data: 0

    Texture: "eyJ0ZXh0dXJlcyI6eyJTS0lOIjp7InVybCI6Imh0dHA6Ly90ZXh0dXJlcy5taW5lY3JhZnQubmV0L3RleHR1cmUvZGZjM2MyNDNmYzA4OTRhYTQwMjhkMzJiMTlhODMwYTJmY2FkYzI5MzI3MGI0Y2IzMmMxYmFlNDJjNzhjMDhiZSJ9fX0="

    Tag: " 

    Displayname: "&r&f{COLOR} &7(tag)"

    Permission: "

    Lore:

      Locked:

      - "&8Books tag"

      - ""

      - "&fPreview:"

      - "&7Player {TAG}"

      - "&7Permission: "

      - ""

      - "&fDescription:"

      - "&

      - ""

      - "&fInformation:"

      - "&7Sorry this tag is &c&nlocked!&7 you can unlock"

      - "&7this tags by &d&ndonating&7 to azurenetwork at"

      - ""

      - "&f&l(!) &7Buy tags at: &f&ndiscord.gg/BzvaXXfUuH"

      - ""

      Unlocked:

      - "&8Books tag"

      - ""

      - "&fPreview:"

      - "&7Player {TAG}"

      - "&7Permission: "

      - ""

      - "&fDescription:"

      - "&

      - ""

      - "&fInformation:"

      - "&7You've &a&nunlocked&7 this tags! congratulations"

      - "&7you can now select this tag ^^!"

      - ""

      - "&cSelected: &c&l✘"

      - ""

      - "&f&n(Right-click)&7 to select the tag"

      - "&f&n(Left-click)&7 to select the tag"

      - ""

      Selected:

      - "&8Books tag"

      - ""

      - "&fPreview:"

      - "&7Player {TAG}"

      - "&7Permission: "

      - ""

      - "&fDescription:"

      - "&

      - ""

      - "&fInformation:"

      - "&7You've &a&nselected&7 this tags! now your"

      - "&7name look amazing ^^!"

      - ""

      - "&aSelected: &a&l✔"

      - ""

    No-permission-message: "&c&l(!) &7You don't have permission to use this tag!"
