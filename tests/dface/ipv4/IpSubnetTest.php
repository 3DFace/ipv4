<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\ipv4;

class IpSubnetTest extends \PHPUnit_Framework_TestCase {

	function testFromShortString(){
		$test = ['1.1.1.1/32', '127.0.0.0/8', '10.0.0.0/16', '194.50.85.0/24', '192.168.0.0/24', '0.0.0.0/0'];
		foreach($test as $subnet_str){
			$subnet = IpSubnet::fromString($subnet_str);
			$this->assertEquals($subnet_str, $subnet->toShortString());
		}
	}

	function testFromLongString(){
		$test = ['1.1.1.0/255.255.255.0', '127.0.0.0/255.0.0.0', '10.0.0.0/255.255.0.0', '194.50.85.0/255.255.255.0', '192.168.0.0/255.255.255.0', '0.0.0.0/0.0.0.0'];
		foreach($test as $subnet_str){
			$subnet = IpSubnet::fromString($subnet_str);
			$this->assertEquals($subnet_str, $subnet->toString());
		}
	}

	function testContainsAddress(){
		$subnet_str = '10.10.10.0/24';
		$subnet = IpSubnet::fromString($subnet_str);

		$contains = ['10.10.10.1', '10.10.10.10', '10.10.10.255'];
		foreach($contains as $ip_str){
			$this->assertTrue($subnet->containsAddress(IpAddress::fromString($ip_str)), "$subnet_str must contain $ip_str");
		}

		$not_contains = ['10.10.11.1', '192.168.0.1', '127.0.0.1'];
		foreach($not_contains as $ip_str){
			$this->assertFalse($subnet->containsAddress(IpAddress::fromString($ip_str)), "$subnet_str must not contain $ip_str");
		}
	}

	function testContainsAll(){
		$subnet_str = '0.0.0.0/0.0.0.0';
		$subnet = IpSubnet::fromString($subnet_str);
		$contains = ['10.10.10.1', '127.0.0.1', '192.168.1.88'];
		foreach($contains as $ip_str){
			$this->assertTrue($subnet->containsAddress(IpAddress::fromString($ip_str)), "$subnet_str must contain $ip_str");
		}
	}

	function testContainsSubnet(){
		$subnet_str = '10.0.0.0/16';
		$subnet = IpSubnet::fromString($subnet_str);

		$contains = ['10.0.0.0/24', '10.0.1.0/24', '10.0.0.1'];
		foreach($contains as $sub_str){
			$this->assertTrue($subnet->containsSubnet(IpSubnet::fromString($sub_str)), "$subnet_str must contain $sub_str");
		}

		$not_contains = ['10.0.0.0/8', '192.168.0.0/24', '127.0.0.1'];
		foreach($not_contains as $sub_str){
			$this->assertFalse($subnet->containsSubnet(IpSubnet::fromString($sub_str)), "$subnet_str must not contain $sub_str");
		}
	}

	function testBroadCast(){
		$test = [
			'10.0.0.0/8' => '10.255.255.255',
			'10.0.0.0/24' => '10.0.0.255',
			'10.0.0.0/30' => '10.0.0.3',
		];
		foreach($test as $subnet_str => $broadcast_str){
			$subnet = IpSubnet::fromString($subnet_str);
			$this->assertEquals($broadcast_str, $subnet->getBroadcastAddress()->toString());
		}
	}

	function testImplicit32Format(){
		$subnet_str = '10.10.10.0';
		$subnet = IpSubnet::fromString($subnet_str);
		$this->assertEquals($subnet_str.'/32', $subnet->toShortString());
	}

	function testInvalidFormatFailed1(){
		$this->setExpectedException(\InvalidArgumentException::class);
		IpSubnet::fromString('10.10.10.10.10');
	}

	function testInvalidFormatFailed2(){
		$this->setExpectedException(\InvalidArgumentException::class);
		IpSubnet::fromString('10.10.10.10/');
	}

	function testInvalidFormatFailed3(){
		$this->setExpectedException(\InvalidArgumentException::class);
		IpSubnet::fromString('10.10.10.10/10.10');
	}

	function testInvalidFormatFailed4(){
		$this->setExpectedException(\InvalidArgumentException::class);
		IpSubnet::fromString('10.10.10.10/10/10');
	}

	function testBadMaskFailed(){
		$this->setExpectedException(\InvalidArgumentException::class);
		IpSubnet::fromString('10.10.10.10/33');
	}

}
