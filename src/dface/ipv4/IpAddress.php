<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\ipv4;

class IpAddress {

	/** @var int */
	protected $address;

	/**
	 * IpV4Address constructor.
	 * @param int $address_int
	 */
	public function __construct($address_int){
		if(!(is_float($address_int) || is_int($address_int))){
			throw new \InvalidArgumentException("Only int allowed, use ".self::class."::fromString to create from string");
		}
		$this->address = (int) $address_int;
	}

	function toLong(){
		return $this->address;
	}

	function toString(){
		return long2ip($this->address);
	}

	function toUnsigned(){
		return sprintf('%u', $this->address);
	}

	static function fromString($ip_str){
		$long = ip2long($ip_str);
		if($long === false){
			throw new \InvalidArgumentException("Bad address format: $ip_str");
		}
		return new IpAddress($long);
	}

	static function compare(IpAddress $i1, IpAddress $i2){
		$a1 = $i1->address;
		$a2 = $i2->address;
		if($a1 === $a2){
			return 0;
		}else{
			$a1 = (float) sprintf('%u', $i1->address);
			$a2 = (float) sprintf('%u', $i2->address);
			$r = $a1 > $a2 ? 1 : -1;
			return $r;
		}
	}

	/**
	 * @param IpAddress[] $addresses
	 */
	static function sort(array &$addresses){
		usort($addresses, [self::class, 'compare']);
	}

	/**
	 * @param IpAddress[] $addresses
	 * @return string[]
	 */
	static function toStringList(array $addresses){
		$list = [];
		foreach($addresses as $ip){
			$list[] = $ip->toString();
		}
		return $list;
	}

}
