# VoiceNews
Make Siri read your News.
Add your feeds to `/data/data.json` and configure a blacklist there. Deploy `htdocs`and add a Siri shortcut to open that URL and read it out.
Open  [voiceNews](https://www.icloud.com/shortcuts/f1d6e06582c947c7a5930df8b470c370) on your iPhone, modify the URL, customize it and you're done.


## Infos for local development
### Usage of Vagrant
1. install vagrant on your machine (https://www.vagrantup.com/)
2. install Virtualbox (https://www.virtualbox.org/wiki/Downloads)
3. head to your local repository an enter `vagrant up`
4. Wait a while until all components are loaded an the box is running. (The first start can take a while)
5. visit (http://127.0.0.1:8080/)
