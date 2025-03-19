version: '3.8'

services:
  ### RabbitMQ (Message Broker)
  rabbitmq:
    image: rabbitmq:3-management
    container_name: rabbitmq
    ports:
      - "5672:5672"   # Port AMQP pour Symfony Messenger
      - "15672:15672" # Interface de gestion RabbitMQ
    environment:
      RABBITMQ_DEFAULT_USER: guest
      RABBITMQ_DEFAULT_PASS: guest

  ### Mercure (WebSockets)
  mercure:
    image: dunglas/mercure
    container_name: mercure
    restart: unless-stopped
    ports:
      - "1337:1337"
    environment:
      # Configuration Mercure
      MERCURE_PUBLISHER_JWT_KEY: "YourSecretKey"
      MERCURE_SUBSCRIBER_JWT_KEY: "YourSecretKey"
      MERCURE_EXTRA_DIRECTIVES: "cors_origins *"
