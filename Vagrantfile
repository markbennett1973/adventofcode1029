# -*- mode: ruby -*-
# vi: set ft=ruby :
Vagrant.require_version ">= 1.8.6"

$provision = <<-SCRIPT
echo Installing PHP...
add-apt-repository ppa:ondrej/php
apt-get update
apt-get --assume-yes install php7.4
apt-get --assume-yes install php-xdebug
SCRIPT

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/xenial64"
  config.vm.provision "shell", inline: $provision
#   config.vm.synced_folder ".", "/home/vagrant"

  # Display the VirtualBox GUI when booting the machine
  config.vm.provider "virtualbox" do |vb|
    # vb.gui = true
    vb.name = "adventofcode2019"
    vb.memory = 1024
  end
end
