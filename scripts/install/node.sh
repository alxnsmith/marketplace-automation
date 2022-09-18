echo "Install nvm"
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash

echo " > nvm install 18"
nvm install 18

echo " > nvm use 18"
nvm use 18

echo " > nvm alias default 18"
nvm alias default 18

echo " > npm config set prefix '~/.npm-global'"
npm config set prefix '~/.npm-global'

# Then add ~/.npm-global/bin to PATH in ~/.profile
echo " > echo 'export PATH=~/.npm-global/bin:$PATH' >> ~/.profile"
echo 'export PATH=~/.npm-global/bin:$PATH' >> ~/.profile

# Then source ~/.profile
echo " > source ~/.profile"
source ~/.profile

echo " > npm install -g yarn"
npm install -g yarn