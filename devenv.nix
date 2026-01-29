{
  pkgs,
  lib,
  config,
  inputs,
  ...
}:

{
  dotenv.enable = false;
  dotenv.disableHint = true;
  # https://devenv.sh/packages/
  packages = [ pkgs.nodejs_24 pkgs.php pkgs.php.packages.composer ];

  languages.php.enable = true;
  languages.javascript.enable = true;
  languages.javascript.package = pkgs.nodejs_24;

  languages.php.version = "8.4";
  languages.php.extensions = [
    "pdo"
    "intl"
    "imap"
    "iconv"
    "xdebug"
    "dom"
    "gd"
    "imagick"
    "xsl"
  ];

  languages.php.ini = ''
      memory_limit = -1;
    '';

  services.mysql.enable = true;

  services.mysql.initialDatabases = [ { name = "vytrvalec"; } ];
  services.mysql.ensureUsers = [
    {
      name = "share";
      password = "share";
      ensurePermissions = {
        "*.*" = "ALL PRIVILEGES";
      };
    }
  ];

  # https://devenv.sh/processes/
  # processes.dev.exec = "${lib.getExe pkgs.watchexec} -n -- ls -la";

  # https://devenv.sh/services/
  # services.postgres.enable = true;

  # https://devenv.sh/scripts/

  # https://devenv.sh/basics/

  # https://devenv.sh/tasks/
  # tasks = {
  #   "myproj:setup".exec = "mytool build";
  #   "devenv:enterShell".after = [ "myproj:setup" ];
  # };

  # https://devenv.sh/tests/

  # https://devenv.sh/git-hooks/
  # git-hooks.hooks.shellcheck.enable = true;

  # See full reference at https://devenv.sh/reference/options/
}
