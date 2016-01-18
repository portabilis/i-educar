
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "portabilis"
  config.vm.box_url = "https://s3-us-west-2.amazonaws.com/portabilis2/public/ieducar/ieducar.box"

  #Compartilhando portas
  config.vm.network "forwarded_port", guest: 80, host: 8081
  config.vm.network "forwarded_port", guest: 5433, host: 5434

  config.vm.provider "virtualbox" do |v|
#      v.gui = true
      v.name = "portabilis"
      v.memory = 2048
  end

  #Compartilhando pasta do repositório
  config.vm.synced_folder ".", "/home/vagrant/ieducar"
end
