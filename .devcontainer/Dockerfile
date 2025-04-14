FROM mcr.microsoft.com/devcontainers/php:8.3

ARG INSTALL_NPM=false

RUN apt-get update && \
    apt-get install -y \
        libjpeg-dev \
        libpng-dev \
        libwebp-dev \
        libfreetype6-dev \
        libzip-dev

RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype && \
    docker-php-ext-install gd zip pdo pdo_mysql exif pcntl bcmath

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN git clone https://github.com/zsh-users/zsh-completions.git /home/vscode/.oh-my-zsh/custom/plugins/zsh-completions && \
    git clone https://github.com/zsh-users/zsh-syntax-highlighting.git /home/vscode/.oh-my-zsh/custom/plugins/zsh-syntax-highlighting && \
    git clone https://github.com/zsh-users/zsh-autosuggestions.git /home/vscode/.oh-my-zsh/custom/plugins/zsh-autosuggestions

RUN curl -L https://cs.symfony.com/download/php-cs-fixer-v3.phar -o /usr/local/bin/php-cs-fixer && \
    chmod +x /usr/local/bin/php-cs-fixer

RUN cp /home/vscode/.zshrc /home/vscode/.zshrc.bak

RUN echo "$(cat /home/vscode/.zshrc)" | awk '{gsub(/plugins=\(git\)/, "plugins=(git zsh-completions zsh-syntax-highlighting zsh-autosuggestions)")}1' > /home/vscode/.zshrc.replaced && mv /home/vscode/.zshrc.replaced /home/vscode/.zshrc

WORKDIR /home/vscode/workspace
