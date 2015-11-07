# -*- mode: ruby -*-
# vi: set ft=ruby :

module OS
    def OS.windows?
        (/cygwin|mswin|mingw|bccwin|wince|emx/ =~ RUBY_PLATFORM) != nil
    end
end

Vagrant.configure(2) do |config|
    config.vm.box = "ubuntu/trusty32"
    config.vm.network "forwarded_port", guest: 80, host: 8081

    if OS.windows?
        config.vm.provision :shell, path: "vagrant/vagrant_bootstrap.sh", args: "nonpm"
    else
        config.vm.provision :shell, path: "vagrant/vagrant_bootstrap.sh", args: "npm"
    end

	if !OS.windows?
		config.trigger.before :destroy do
			run "vagrant/destroy.sh"
		end
	end
end
