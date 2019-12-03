# -*- mode: ruby -*-
# vi: set ft=ruby :
Vagrant.require_version ">= 1.8.6"

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/xenial64"
  
  config.vm.provision "shell", inline: <<-SHELL
  add-apt-repository ppa:ondrej/php
  apt-get update
  apt-get -y install php7.4
  apt-get -y install php-xdebug

  echo "" >> /home/vagrant/.bashrc
  echo "cd /vagrant" >> /home/vagrant/.bashrc
  echo "xdebug.remote_enable=1" >> /etc/php/7.4/mods-available/xdebug.ini
  echo "xdebug.remote_host=10.0.2.2" >> /etc/php/7.4/mods-available/xdebug.ini
  echo "xdebug.remote_port=9000" >> /etc/php/7.4/mods-available/xdebug.ini
  SHELL

  config.vm.provider "virtualbox" do |vb|
    vb.name = "adventofcode2019"
    vb.memory = 1024
  end
end
