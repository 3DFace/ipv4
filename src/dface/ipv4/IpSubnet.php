<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\ipv4;

class IpSubnet {

	/** @var int */
	protected $address;
	/** @var int */
	protected $mask;

	/**
	 * IpSubnet constructor.
	 * @param int $address_int
	 * @param int $mask_int
	 */
	public function __construct($address_int, $mask_int){
		if(!(is_float($address_int) || is_int($address_int))){
			throw new \InvalidArgumentException("Only int address allowed, use ".self::class."::fromString to create from string");
		}
		$this->address = (int)$address_int;
		if(!(is_float($mask_int) || is_int($mask_int))){
			throw new \InvalidArgumentException("Only int masks allowed, use ".self::class."::fromString to create from string");
		}
		$this->mask = (int)$mask_int;
		if($this->address !== ($this->address & $this->mask)){
			throw new \InvalidArgumentException("Invalid address/mask combination");
		}
	}

	function containsSubnet(IpSubnet $subnet){
		return ($this->mask < $subnet->mask) && (($subnet->address & $this->mask) === $this->address);
	}

	function containsAddress(IpAddress $ip){
		$long = $ip->toLong();
		return (($long & $this->mask) === ($this->address));
	}

	function getBroadcastAddress(){
		return new IpAddress(~$this->mask | $this->address);
	}

	function toString(){
		return long2ip($this->address).'/'.long2ip($this->mask);
	}

	function toShortString(){
		$bin = decbin($this->mask);
		$m = 0;
		while($bin[$m] === '1'){
			$m++;
		}
		return long2ip($this->address).'/'.$m;
	}

	static function fromString($subnet_str){
		$arr = explode("/", $subnet_str);
		$count = count($arr);
		if($count === 2){
			$mask = $arr[1];
			if(is_numeric($mask)){
				// 10.10.10.0/24
				if($mask < 0 || $mask > 32){
					throw new \InvalidArgumentException("CIDR mask value must be in [0-32] range: $arr[1]");
				}
				$mask = bindec(str_repeat("1", $mask).str_repeat("0", 32 - $mask));;
			}else{
				// 10.10.10.0/255.255.255.0
				$mask = ip2long($mask);
				if($mask === false){
					throw new \InvalidArgumentException("Invalid mask format: $arr[1]");
				}
			}
		}elseif($count === 1){
			$mask = 0xffffffff;
		}else{
			throw new \InvalidArgumentException("Invalid subnet format");
		}
		$address = ip2long($arr[0]);
		if($address === false){
			throw new \InvalidArgumentException("Invalid address format: $arr[0]");
		}
		return new IpSubnet($address, $mask);
	}

}
