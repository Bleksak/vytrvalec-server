# Měsíční Vytrvalec server

## Requirements:
- PHP 8.2+
- NodeJS 21.2.0+

## Prerequisties
- Install composer (ex. `paru -S composer`)
- Install PHP required extensions - php-imagick, php-xsl, php-ffi, php-iconv
- Inside php.ini enable extensions:

       extension=ffi
       extension=gd
       extension=gettext
       extension=iconv
       extension=imagick
       extension=xsl
       extension=pdo_mysql (For MySQL database)
    
## Setup
- Create submission images upload folder `mkdir public/uploads` - Otherwise you will not be able to upload images
- `cp .env .env.local`
- Fill .env.local with your local environment variables (database, jwt token, smtp, firebase token)
- `composer install`
- `npm ci`
- `php bin/console doctrine:migrations:migrate`
- `php bin/console doctrine:database:create`
- `php bin/console doctrine:fixtures:load`

## How to start the server
- Install  symfony-cli - `paru -S xdebug symfony-cli`
- Run with `symfony server:start {..args}`

### ZČU VPN connection
- On Linux, install NetworkManager - `paru -S networkmanager-openconnect`
- In your VPN client create VPN Cisco AnyConnect connection
- Set gateway as `vpn.zcu.cz`
- Username and password is your orion login

### Kubernetes
- Install kubectl `paru -S kubectl`
- You can follow this [instructions](https://helpdesk.zcu.cz/index.php/Kubernetes) or continue as in this readme
- Create config file in `$HOME/.kube/config` as below 
```
  apiVersion: v1
  clusters:
  - cluster:
      certificate-authority-data:
  LS0tLS1CRUdJTiBDRVJUSUZJQ0FURS0tLS0tCk1JSUN5RENDQWJDZ0F3SUJBZ0lCQURBTkJna3Foa2lHOXcwQkFRc0ZBREFWTVJNd0VRWURWUVFERXdwcmRXSmwKY201bGRHVnpNQjRYRFRJd01EY3hOREV5TXpnMU0xb1hEVE13TURjeE1qRXlNemcxTTFvd0ZURVRNQkVHQTFVRQpBeE1LYTNWaVpYSnVaWFJsY3pDQ0FTSXdEUVlKS29aSWh2Y05BUUVCQlFBRGdnRVBBRENDQVFvQ2dnRUJBTmpMCnNxam9lMjNQRGVtYkROQmcvUU5MVVRDWklMakVzemEyVW8rTTdyNUtqcjFsSHo5WDBSczBVeTZZN0VCMmQxTDYKZVBrbkV6NmFRU3BBUTlXUVBWYjZNVy9PMU9Ha1QvRkhjdVJaZ1JPUGlNSmt2Q1BETmIzNjJsTDVDVG5iaGFvdQpTZk83SmFDTVlweHdzQmNlUnB2MnhjZTRNTnc3L3pBbzZYTElXdDQ4RjVldzJpTnZDODB1Y2llSFRvWjhhUzR1CmVkbGhWNGxoUU9lK1dJazFkeHBsU1NRYys0TEJYeUlEOVpPbXRSb1F2emN4dlJGQ1ZYQUZ1RWRNd3dsMTZEWkYKWVlYU0c5TUI1SmNjOTVqUTVkWUg1dzZta0s4NTJQakJ0TDQ0dWhMRDFJNW9wTEUrcU1tL2hYaUk2QmFKZk1CWgp4b1oxR3pRRDdGKzcrZXdacE5zQ0F3RUFBYU1qTUNFd0RnWURWUjBQQVFIL0JBUURBZ0trTUE4R0ExVWRFd0VCCi93UUZNQU1CQWY4d0RRWUpLb1pJaHZjTkFRRUxCUUFEZ2dFQkFEVHVoazNyUWRvRkxVWXBPWlc1UytxL3lpVmcKdS9TaUlocWNnMDRJb1dNdWpTaTRwQnhOVTg2MEY3ODJXWU9FTzVzUE5FNjZaRDJWZ1NpVlJMd2RtU2M2QlpxSgpZU3NxeU42NlN1ajhZd1AydzdzL09HYzVEWGN6RElMOWNuSDJsRmJ5SHBhL1ZVbmNHL1VRelAyS0VENDNVZnBpCm1CZ1B2dGtjQXdXcDJQcnIrTjljQjV5ZWtTNXRYK2drQ25VS2ZhVWw0eGFoK1BTZGU3VEFjZ0hiYm1uSWxPdTEKQTh2QmhSYjBRNitnMjEzcktZcVlQVThFQVVydERSczVhMGFWMnBmNlBJbitWdEJvMVhNcWdVeG1yM2diNE1IUgpzczRHWGY2RTRPSTZaOEo5akMzR2lwRWVzUkVHd01WK3RIWXBuQ2t0dHVWTFVZT3JqUmp4Z2FlVmdJZz0KLS0tLS1FTkQgQ0VSVElGSUNBVEUtLS0tLQo=
      server: https://synergia.civ.zcu.cz:6443
    name: synergia
  contexts:
  - context:
      cluster: synergia
      namespace: vytrvalec-kts
      user: gitlab-vytrvalec-kts
    name: vytrvalec-kts
  current-context: vytrvalec-kts
  kind: Config
  preferences: {}
  users:
  - name: gitlab-vytrvalec-kts
    user:
      token: <token for gitlab-vytrvalec-kts>
  ```
- Replace `<token for gitlab-vytrvalec-kts>` with token obtained from [Kubernetes Dashboard](https://dashboard.kube.zcu.cz/#/workloads?namespace=default) under vytrvalec-kts workspace -> config and storage -> secrets -> gitlab-vytrvalec-kts-token 
  
  ![img.png](readme/img.png)

- Now you can run commands like `kubectl exec ...`


### Run before push 
- `vendor/bin/phpstan analyze` - Static analysis
- `vendor/bin/php-cs-fixer fix` - Formatting



