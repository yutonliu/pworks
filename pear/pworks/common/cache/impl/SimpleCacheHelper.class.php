<?php
/*
 * Copyright 2008 - 2015 Milo Liu<cutadra@gmail.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *    1. Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *
 *    2. Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 */

require_once('pworks/common/cache/ICacheHelper.iface.php');
require_once('pworks/common/cache/impl/ArrayCache.class.php');


/**
 * Modified by Milo<cutadra@gmail.com> on Aug. 06, 2016
 * 修改主要的设置方法, 使其返回值为相应的实例, 为链式调用提供机制
 */
class SimpleCacheHelper implements ICacheHelper{

	private $_caches;
	private $_group;

	public static function getInstance(){
		return new SimpleCacheHelper();
	}

	public function __construct(){
		$this->_caches = array();
		$this->_caches[0] = new ArrayCache();
	}

	public function setCache(ICache $cache, $level=0){
		$this->_caches[$level] = $cache;
		ksort($this->_caches);
		return $this;
	}

	public function setGroup($group){
		$this->_group = $group;
		return $this;
	}

    public function dump(){
        foreach($this->_caches as $cache){
            $cache->dump();
        }
    }
	public function store($key, $object, $group=''){
		if($group == ''){
			$group = $this->_group;
		}
		$gKey = $group .'_' . $key;
		foreach($this->_caches as $cache){
			$cache->store($gKey, $object);
		}

	}

	public function fetch($key, $group=''){
		if($group == ''){
			$group = $this->_group;
		}
		$gKey = $group .'_' . $key;
		foreach($this->_caches as $cache){
			$object = $cache->fetch($gKey);
			if($object!== false){
				return $object;
			}
		}
		return false;
	}

	public function delete($key, $group=''){
		if($group == ''){
			$group = $this->_group;
		}
		$gKey = $group .'_' . $key;
		foreach($this->_caches as $cache){
			$cache->delete($gKey, $object);
		}
	}

	public function __destruct(){
		foreach($this->_caches as $cache){
			$cache->close();
		}
	}
}
