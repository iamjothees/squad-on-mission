FROM iamjothees/laravel-image:php8.5

ENV NVM_DIR="/usr/local/nvm"
RUN source $NVM_DIR/nvm.sh \
    && nvm install 22 \
    && nvm alias default 22 \
    && nvm use default

# Ensure Node 22 is used for npm commands
ENV PATH="/usr/local/nvm/versions/node/v22.23.2/bin:${PATH}"

EXPOSE 5173