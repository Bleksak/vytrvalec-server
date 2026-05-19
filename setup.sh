#!/usr/bin/env bash
set -euo pipefail

# Vytrvalec Server - One-command setup
# Installs: just, fnm+Node.js, FrankenPHP, Composer
# Uses FrankenPHP's bundled PHP for everything (no system PHP needed)

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

info() { echo -e "${CYAN}[setup]${NC} $*"; }
ok()   { echo -e "${GREEN}[✓]${NC}     $*"; }
warn() { echo -e "${YELLOW}[!]${NC}     $*"; }
fail() { echo -e "${RED}[✗]${NC} $*"; exit 1; }

command_exists() {
    command -v "$1" &>/dev/null
}

# --- Distro detection ---

detect_distro() {
    if [[ -f /etc/os-release ]]; then
        . /etc/os-release
        DISTRO="${ID,,}"
    elif command_exists pacman; then
        DISTRO="arch"
    elif command_exists dnf; then
        DISTRO="fedora"
    elif command_exists apt-get; then
        DISTRO="ubuntu"
    else
        fail "Unable to detect Linux distribution"
    fi
    info "Detected: $DISTRO ${VERSION_ID:-unknown}"
}

# --- Package managers ---

install_pkg_apt() {
    sudo apt-get update -qq
    sudo apt-get install -y --no-install-recommends "$@"
}

install_pkg_dnf() {
    sudo dnf install -y "$@"
}

install_pkg_zypper() {
    sudo zypper install -y "$@"
}

install_pkg_pacman() {
    sudo pacman -S --needed --noconfirm "$@"
}

# --- Install system packages (sqlite3 CLI only) ---

install_system_packages() {
    # Skip if everything is already installed
    if command_exists sqlite3 && command_exists convert; then
        ok "System packages already present"
        return 0
    fi

    info "Installing system packages..."
    case "$DISTRO" in
        ubuntu|debian|linux*mint*)
            install_pkg_apt sqlite3 imagemagick libmagickwand-dev ghostscript || warn "Failed to install system packages"
            ;;
        fedora|rhel|centos*)
            install_pkg_dnf sqlite ImageMagick ImageMagick-devel ghostscript || warn "Failed to install system packages"
            ;;
        opensuse*|sles*)
            install_pkg_zypper sqlite3 imagemagick imagemagick-devel ghostscript || warn "Failed to install system packages"
            ;;
        arch|manjaro*|endeavouros|cachyos)
            install_pkg_pacman sqlite imagemagick ghostscript || warn "Failed to install system packages"
            ;;
        *)
            warn "Unknown distro ($DISTRO) - trying apt..."
            install_pkg_apt sqlite3 imagemagick ghostscript 2>/dev/null || warn "Could not install system packages"
            ;;
    esac
    ok "System packages installed"
}

# --- Install `just` ---

install_just() {
    if command_exists just; then
        ok "just: $(just --version 2>&1 | head -1)"
        return 0
    fi

    info "Installing just..."

    # Try package managers first
    case "$DISTRO" in
        ubuntu|debian|linux*mint*)
            if install_pkg_apt just 2>/dev/null; then ok "just installed via apt"; return 0; fi ;;
        fedora|rhel|centos*)
            if install_pkg_dnf just 2>/dev/null; then ok "just installed via dnf"; return 0; fi ;;
        arch|manjaro*|endeavouros|cachyos)
            install_pkg_pacman just; ok "just installed via pacman"; return 0 ;;
        opensuse*|sles*)
            if install_pkg_zypper just 2>/dev/null; then ok "just installed via zypper"; return 0; fi ;;
    esac

    # Fallback: download musl static binary
    info "Downloading just from GitHub..."
    local ver
    ver=$(curl -s https://api.github.com/repos/justcommand/just/releases/latest \
        | grep '"tag_name"' | sed -E 's/.*"v([^"]+)".*/\1/')
    info "Installing just v$ver..."
    curl -sL "https://github.com/justcommand/just/releases/latest/download/just-${ver}-x86_64-unknown-linux-musl.tar.gz" \
        -o /tmp/just.tar.gz
    tar -xzf /tmp/just.tar.gz -C /tmp
    sudo mv "/tmp/just-${ver}-x86_64-unknown-linux-musl/just" /usr/local/bin/just
    rm -rf /tmp/just*
    ok "just installed"
}

# --- Install FrankenPHP ---

install_frankenphp() {
    if command_exists frankenphp; then
        ok "FrankenPHP: $(frankenphp version 2>&1 | head -1)"
        return 0
    fi

    info "Installing FrankenPHP..."
    curl -fsSL https://frankenphp.dev/install.sh | sh
    [[ -f ./frankenphp ]] && sudo mv ./frankenphp /usr/local/bin/frankenphp
    ok "FrankenPHP installed"
}

# --- Install fnm + Node.js ---

install_fnm_node() {
    if command_exists fnm; then
        ok "fnm already installed"
    else
        info "Installing fnm..."
        curl -fsSL https://fnm.vercel.app/install | bash

        local fnm_sh="$HOME/.local/share/fnm/fnm.sh"
        [[ ! -f "$fnm_sh" ]] && fnm_sh="$HOME/.cache/fnm/fnm.sh"

        if ! grep -q "fnm" ~/.bashrc 2>/dev/null; then
            cat >> ~/.bashrc << 'SHELLEOF'

# fnm
if [ -f "$HOME/.local/share/fnm/fnm.sh" ]; then
    eval "$("$HOME/.local/share/fnm/fnm" env --use-on-cd --shell bash)"
elif [ -f "$HOME/.cache/fnm/fnm.sh" ]; then
    eval "$("$HOME/.cache/fnm/fnm" env --use-on-cd --shell bash)"
fi
SHELLEOF
            ok "Added fnm to ~/.bashrc"
        fi
        ok "fnm installed"
    fi

    # Source fnm
    local fnm_sh="$HOME/.local/share/fnm/fnm.sh"
    [[ ! -f "$fnm_sh" ]] && fnm_sh="$HOME/.cache/fnm/fnm.sh"
    eval "$("$fnm_sh" 2>/dev/null || echo "")"
    # Alternative source method
    command -v fnm &>/dev/null || {
        if [[ -x "$HOME/.local/share/fnm/fnm" ]]; then
            export PATH="$HOME/.local/share/fnm:$PATH"
        elif [[ -x "$HOME/.cache/fnm/fnm" ]]; then
            export PATH="$HOME/.cache/fnm:$PATH"
        fi
    }

    # Install Node.js
    if command_exists node; then
        info "Node.js already installed: $(node -v)"
    elif command_exists fnm; then
        info "Installing Node.js LTS via fnm..."
        fnm install --lts
        fnm default --lts
        ok "Node.js: $(node -v)"
    else
        warn "Cannot install Node.js - install fnm manually or install Node.js via your package manager"
    fi
}

# --- Install Composer ---

install_composer() {
    local fp="frankenphp php-cli"
    local bin_dir="${JUSTFILE_DIR}/bin"

    if [[ -f "${bin_dir}/composer.phar" ]] && [[ -f "${bin_dir}/composer" ]]; then
        ok "Composer already installed in bin/"
        return 0
    fi

    info "Installing Composer..."
    mkdir -p "$bin_dir"

    # Download composer.phar directly
    curl -fsSL https://getcomposer.org/download/latest-stable/composer.phar \
        -o "${bin_dir}/composer.phar"

    # Create wrapper that uses FrankenPHP instead of system PHP
    cat > "${bin_dir}/composer" << 'COMPOSEREOF'
#!/usr/bin/env bash
exec frankenphp php-cli "$(dirname "$0")/composer.phar" "$@"
COMPOSEREOF
    chmod +x "${bin_dir}/composer"

    ok "Composer installed to bin/"
}

# --- Verify ---

verify() {
    echo ""
    info "Verifying installation..."

    [[ -x "$(command -v just)" ]] && ok "just $(just --version 2>&1 | head -1)" || fail "just missing"
    [[ -x "$(command -v frankenphp)" ]] && ok "FrankenPHP $(frankenphp version 2>&1 | head -1)" || fail "FrankenPHP missing"

    local fp_ver
    fp_ver=$(frankenphp php-cli -r 'echo PHP_VERSION;' 2>/dev/null)
    [[ -n "$fp_ver" ]] && ok "PHP $fp_ver (via FrankenPHP)" || fail "FrankenPHP PHP not working"

    [[ -x "$(command -v sqlite3)" ]] && ok "sqlite3" || warn "sqlite3 CLI not found (optional)"
    [[ -x "$(command -v node)" ]] && ok "Node.js $(node -v)" || warn "Node.js not found"
    [[ -x "$(command -v npm)" ]] && ok "npm $(npm -v)" || warn "npm not found"
    [[ -f "${JUSTFILE_DIR}/bin/composer" ]] && ok "Composer (bin/)" || warn "Composer not found in bin/"
}

# --- Main ---

main() {
    JUSTFILE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

    echo ""
    echo -e "${CYAN}╔══════════════════════════════════════════╗${NC}"
    echo -e "${CYAN}║   Vytrvalec Server - Setup              ║${NC}"
    echo -e "${CYAN}╚══════════════════════════════════════════╝${NC}"
    echo ""

    detect_distro

    # Check if all tools already exist - if so, skip to project setup
    local need_sudo=false
    command_exists just || need_sudo=true
    command_exists frankenphp || need_sudo=true
    command_exists sqlite3 || need_sudo=true

    if [[ "$need_sudo" == "true" ]] && [[ $EUID -ne 0 ]]; then
        if [[ -t 0 ]]; then
            sudo -v || warn "sudo access required for system packages (some features may not install)"
        else
            warn "No terminal for sudo - system packages will be skipped"
        fi
    fi

    install_system_packages
    install_just
    install_frankenphp
    install_fnm_node
    install_composer
    verify

    echo ""
    echo -e "${GREEN}Setup complete!${NC}"
    echo ""
    echo -e "  Start the server:  ${CYAN}just run${NC}"
    echo -e "  All commands:      ${CYAN}just${NC}"
    echo ""
}

main "$@"
