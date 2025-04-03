#!/bin/bash
# ======================================================
# Laravel Project Management Script
# ======================================================
# Questo script gestisce:
# - Backup del codice e del database
# - Versioning tramite Git
# - Deployment in produzione
# ======================================================

# Configurazione
PROJECT_NAME="dynamic-crud-manager"
DB_USER="root"              # Cambia con il tuo utente database
DB_PASSWORD=""              # Cambia con la tua password database
DB_NAME="dynamic_crud_manager"   # Cambia con il nome del tuo database
BACKUP_DIR="../backup"    # Cambia con il percorso dove salvare i backup
PRODUCTION_SERVER="user@your-server.com"  # Cambia con i dettagli del tuo server
PRODUCTION_PATH="/var/www/dynamic-crud-manager"  # Cambia con il percorso sul server

# Configurazione Git
GIT_REPO_URL="https://github.com/dcintur/dynamic-crud-manager.git"            # URL del repository Git (es. https://github.com/username/repo.git)
GIT_USERNAME="dcintur"            # Il tuo nome utente Git
GIT_EMAIL="daniele.cintura@gmail.com"               # La tua email Git
GIT_TOKEN="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"               # Il tuo token di accesso personale (per GitHub, GitLab, etc.)

# Colori
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# ==== FUNZIONI ====

# Funzione per configurare Git
setup_git() {
    # Verifica se Git è già configurato
    local current_user=$(git config --get user.name)
    local current_email=$(git config --get user.email)
    
    if [ -z "$current_user" ] || [ -z "$current_email" ] || [ "$current_user" != "$GIT_USERNAME" ] || [ "$current_email" != "$GIT_EMAIL" ]; then
        echo -e "${YELLOW}Setting up Git configuration...${NC}"
        
        # Se le credenziali non sono state impostate nella configurazione, chiedi all'utente
        if [ -z "$GIT_USERNAME" ]; then
            echo -e "${YELLOW}Enter your Git username:${NC}"
            read GIT_USERNAME
        fi
        
        if [ -z "$GIT_EMAIL" ]; then
            echo -e "${YELLOW}Enter your Git email:${NC}"
            read GIT_EMAIL
        fi
        
        git config user.name "$GIT_USERNAME"
        git config user.email "$GIT_EMAIL"
        
        echo -e "${GREEN}Git configuration updated.${NC}"
    fi
    
    # Configurazione del repository remoto
    if [ -n "$GIT_REPO_URL" ]; then
        local current_remote=$(git remote get-url origin 2>/dev/null || echo "")
        
        if [ -z "$current_remote" ]; then
            echo -e "${YELLOW}Setting up Git remote repository...${NC}"
            git remote add origin "$GIT_REPO_URL"
            echo -e "${GREEN}Remote repository added.${NC}"
        elif [ "$current_remote" != "$GIT_REPO_URL" ]; then
            echo -e "${YELLOW}Updating Git remote repository...${NC}"
            git remote set-url origin "$GIT_REPO_URL"
            echo -e "${GREEN}Remote repository updated.${NC}"
        fi
    fi
    
    # Configura credenziali per l'helper di credenziali se il token è disponibile
    if [ -n "$GIT_TOKEN" ]; then
        # Per GitHub
        if [[ "$GIT_REPO_URL" == *github.com* ]]; then
            git config credential.helper store
            echo "https://$GIT_USERNAME:$GIT_TOKEN@github.com" > ~/.git-credentials
            chmod 600 ~/.git-credentials
            echo -e "${GREEN}GitHub credentials stored.${NC}"
        # Per GitLab
        elif [[ "$GIT_REPO_URL" == *gitlab.com* ]]; then
            git config credential.helper store
            echo "https://oauth2:$GIT_TOKEN@gitlab.com" > ~/.git-credentials
            chmod 600 ~/.git-credentials
            echo -e "${GREEN}GitLab credentials stored.${NC}"
        fi
    fi
}

# Funzione per mostrare il menu
show_menu() {
    clear
    echo -e "${BLUE}======================================================${NC}"
    echo -e "${BLUE}      LARAVEL PROJECT MANAGEMENT SCRIPT               ${NC}"
    echo -e "${BLUE}======================================================${NC}"
    echo -e "${BLUE}Project: ${GREEN}$PROJECT_NAME${NC}"
    echo -e "${BLUE}Current Version: ${GREEN}$(get_current_version)${NC}"
    if [ -d ".git" ]; then
        echo -e "${BLUE}Git User: ${GREEN}$(git config --get user.name)${NC}"
        echo -e "${BLUE}Git Remote: ${GREEN}$(git remote get-url origin 2>/dev/null || echo "Not set")${NC}"
    fi
    echo -e "${BLUE}======================================================${NC}"
    echo -e "${YELLOW}1. Create backup${NC}"
    echo -e "${YELLOW}2. Deploy to production${NC}"
    echo -e "${YELLOW}3. Create a new version${NC}"
    echo -e "${YELLOW}4. Restore from backup${NC}"
    echo -e "${YELLOW}5. View version history${NC}"
    echo -e "${YELLOW}6. Build assets${NC}"
    echo -e "${YELLOW}7. Run tests${NC}"
    echo -e "${YELLOW}8. Configure Git credentials${NC}"
    echo -e "${YELLOW}9. Push to remote repository${NC}"
    echo -e "${YELLOW}0. Exit${NC}"
    echo -e "${BLUE}======================================================${NC}"
    echo -e "Enter your choice [0-9]: "
    read choice
    process_choice $choice
}

# Funzione per processare la scelta dell'utente
process_choice() {
    case $1 in
        1) create_backup ;;
        2) deploy_to_production ;;
        3) create_new_version ;;
        4) restore_from_backup ;;
        5) view_version_history ;;
        6) build_assets ;;
        7) run_tests ;;
        8) configure_git ;;
        9) push_to_remote ;;
        0) echo -e "${GREEN}Goodbye!${NC}"; exit 0 ;;
        *) echo -e "${RED}Invalid choice. Please try again.${NC}"; sleep 2; show_menu ;;
    esac
}

# Funzione per configurare Git manualmente
configure_git() {
    clear
    echo -e "${BLUE}Configure Git Credentials${NC}"
    
    echo -e "${YELLOW}Enter your Git username:${NC}"
    read input_username
    if [ -n "$input_username" ]; then
        GIT_USERNAME="$input_username"
        git config user.name "$GIT_USERNAME"
    fi
    
    echo -e "${YELLOW}Enter your Git email:${NC}"
    read input_email
    if [ -n "$input_email" ]; then
        GIT_EMAIL="$input_email"
        git config user.email "$GIT_EMAIL"
    fi
    
    echo -e "${YELLOW}Enter Git repository URL (e.g., https://github.com/username/repo.git):${NC}"
    read input_repo
    if [ -n "$input_repo" ]; then
        GIT_REPO_URL="$input_repo"
        
        local current_remote=$(git remote get-url origin 2>/dev/null || echo "")
        if [ -z "$current_remote" ]; then
            git remote add origin "$GIT_REPO_URL"
        else
            git remote set-url origin "$GIT_REPO_URL"
        fi
    fi
    
    echo -e "${YELLOW}Do you want to store a Personal Access Token? (y/n)${NC}"
    read store_token
    if [[ $store_token == "y" ]]; then
        echo -e "${YELLOW}Enter your Personal Access Token:${NC}"
        read -s input_token
        if [ -n "$input_token" ]; then
            GIT_TOKEN="$input_token"
            
            if [[ "$GIT_REPO_URL" == *github.com* ]]; then
                git config credential.helper store
                echo "https://$GIT_USERNAME:$GIT_TOKEN@github.com" > ~/.git-credentials
                chmod 600 ~/.git-credentials
            elif [[ "$GIT_REPO_URL" == *gitlab.com* ]]; then
                git config credential.helper store
                echo "https://oauth2:$GIT_TOKEN@gitlab.com" > ~/.git-credentials
                chmod 600 ~/.git-credentials
            fi
        fi
    fi
    
    echo -e "${GREEN}Git credentials configured successfully!${NC}"
    read -p "Press Enter to continue..."
    show_menu
}

# Funzione per fare push al repository remoto
push_to_remote() {
    clear
    echo -e "${BLUE}Push to Remote Repository${NC}"
    
    # Verifica se ci sono modifiche da committare
    if ! git diff-index --quiet HEAD --; then
        echo -e "${YELLOW}You have uncommitted changes. Would you like to commit them? (y/n)${NC}"
        read commit_changes
        
        if [[ $commit_changes == "y" ]]; then
            echo -e "${YELLOW}Enter commit message:${NC}"
            read commit_msg
            
            if [ -z "$commit_msg" ]; then
                commit_msg="Update $(date +"%Y-%m-%d %H:%M:%S")"
            fi
            
            git add .
            git commit -m "$commit_msg"
        else
            echo -e "${RED}Please commit or stash your changes before pushing.${NC}"
            read -p "Press Enter to continue..."
            show_menu
            return
        fi
    fi
    
    # Verifica se remote è configurato
    if ! git remote get-url origin &>/dev/null; then
        echo -e "${RED}Remote repository is not configured.${NC}"
        echo -e "${YELLOW}Would you like to configure it now? (y/n)${NC}"
        read configure_now
        
        if [[ $configure_now == "y" ]]; then
            configure_git
        else
            read -p "Press Enter to continue..."
            show_menu
            return
        fi
    fi
    
    # Prendi la branch corrente
    current_branch=$(git rev-parse --abbrev-ref HEAD)
    
    echo -e "${YELLOW}Pushing to remote repository (branch: $current_branch)...${NC}"
    git push -u origin $current_branch
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}Push completed successfully!${NC}"
    else
        echo -e "${RED}Push failed. Check your credentials and repository settings.${NC}"
    fi
    
    read -p "Press Enter to continue..."
    show_menu
}

# Altre funzioni rimangono invariate...
# Funzione per ottenere la versione corrente
get_current_version() {
    if [ -f "version.txt" ]; then
        cat version.txt
    else
        echo "0.1.0"
    fi
}

# Funzione per incrementare la versione
increment_version() {
    local version=$(get_current_version)
    local major=$(echo $version | cut -d. -f1)
    local minor=$(echo $version | cut -d. -f2)
    local patch=$(echo $version | cut -d. -f3)
    
    echo -e "${YELLOW}Current version is $version${NC}"
    echo -e "${YELLOW}What kind of release is this?${NC}"
    echo -e "${YELLOW}1. Major (breaking changes)${NC}"
    echo -e "${YELLOW}2. Minor (new features, non-breaking)${NC}"
    echo -e "${YELLOW}3. Patch (bug fixes)${NC}"
    read -p "Enter your choice [1-3]: " ver_choice
    
    case $ver_choice in
        1) major=$((major + 1)); minor=0; patch=0 ;;
        2) minor=$((minor + 1)); patch=0 ;;
        3) patch=$((patch + 1)) ;;
        *) echo -e "${RED}Invalid choice. Version not changed.${NC}"; return ;;
    esac
    
    local new_version="$major.$minor.$patch"
    echo $new_version > version.txt
    echo -e "${GREEN}Version updated to $new_version${NC}"
    return 0
}

# Funzione per creare un backup
create_backup() {
    clear
    echo -e "${BLUE}Creating backup...${NC}"
    
    # Crea directory di backup se non esiste
    if [ ! -d "$BACKUP_DIR" ]; then
        mkdir -p "$BACKUP_DIR"
    fi
    
    timestamp=$(date +"%Y%m%d_%H%M%S")
    version=$(get_current_version)
    backup_filename="${PROJECT_NAME}_${version}_${timestamp}"
    backup_path="${BACKUP_DIR}/${backup_filename}"
    
    # Crea directory di backup temporanea
    mkdir -p "$backup_path"
    
    echo -e "${YELLOW}Backing up code...${NC}"
    # Esclude le directory non necessarie
    rsync -a --exclude 'vendor' --exclude 'node_modules' --exclude '.git' \
        --exclude 'storage/logs/*' --exclude 'storage/framework/cache/*' \
        --exclude 'public/build' --exclude 'public/hot' \
        ./ "$backup_path/code/"
    
    # Backup del database
    echo -e "${YELLOW}Backing up database...${NC}"
    if [ -z "$DB_PASSWORD" ]; then
        mysqldump -u "$DB_USER" "$DB_NAME" > "$backup_path/database.sql"
    else
        mysqldump -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" > "$backup_path/database.sql"
    fi
    
    # Salva il file .env
    echo -e "${YELLOW}Backing up environment file...${NC}"
    cp .env "$backup_path/.env.backup"
    
    # Crea un archivio
    echo -e "${YELLOW}Creating archive...${NC}"
    cd "$BACKUP_DIR"
    tar -czf "${backup_filename}.tar.gz" "$backup_filename"
    rm -rf "$backup_filename"
    
    echo -e "${GREEN}Backup created successfully at ${BACKUP_DIR}/${backup_filename}.tar.gz${NC}"
    read -p "Press Enter to continue..."
    show_menu
}

# Funzione per eseguire il deploy in produzione
deploy_to_production() {
    clear
    echo -e "${BLUE}Deploying to production...${NC}"
    
    # Verifica se ci sono modifiche non committate
    if ! git diff-index --quiet HEAD --; then
        echo -e "${RED}You have uncommitted changes. Commit or stash them before deploying.${NC}"
        read -p "Press Enter to continue..."
        show_menu
        return
    fi
    
    # Build degli asset per la produzione
    echo -e "${YELLOW}Building assets for production...${NC}"
    npm run build
    
    # Crea una nuova versione
    echo -e "${YELLOW}Would you like to create a new version before deploying? (y/n)${NC}"
    read create_version
    if [[ $create_version == "y" ]]; then
        create_new_version
    fi
    
    # Usa rsync per il deploy
    echo -e "${YELLOW}Deploying code to server...${NC}"
    rsync -avz --exclude 'vendor' --exclude 'node_modules' --exclude '.git' \
        --exclude 'storage/logs/*' --exclude 'storage/framework/cache/*' \
        ./ "$PRODUCTION_SERVER:$PRODUCTION_PATH"
    
    # Comandi remoti per completare il deploy
    echo -e "${YELLOW}Running post-deploy commands on server...${NC}"
    ssh $PRODUCTION_SERVER "cd $PRODUCTION_PATH && \
        composer install --no-dev --optimize-autoloader && \
        php artisan migrate --force && \
        php artisan config:cache && \
        php artisan route:cache && \
        php artisan view:cache && \
        php artisan optimize"
    
    echo -e "${GREEN}Deployment completed successfully!${NC}"
    read -p "Press Enter to continue..."
    show_menu
}

# Funzione per creare una nuova versione
create_new_version() {
    clear
    echo -e "${BLUE}Creating a new version...${NC}"
    
    # Incrementa la versione
    if ! increment_version; then
        read -p "Press Enter to continue..."
        show_menu
        return
    fi
    
    version=$(get_current_version)
    
    # Richiedi messaggio di changelog
    echo -e "${YELLOW}Enter changelog message (optional):${NC}"
    read changelog_message
    
    if [ -z "$changelog_message" ]; then
        changelog_message="Version $version"
    fi
    
    # Aggiorna file CHANGELOG.md se esiste
    if [ -f "CHANGELOG.md" ]; then
        echo -e "\n## Version $version - $(date +"%Y-%m-%d")\n\n$changelog_message\n\n$(cat CHANGELOG.md)" > CHANGELOG.md
    else
        echo -e "# Changelog\n\n## Version $version - $(date +"%Y-%m-%d")\n\n$changelog_message" > CHANGELOG.md
    fi
    
    # Commit delle modifiche
    echo -e "${YELLOW}Committing changes...${NC}"
    git add version.txt CHANGELOG.md
    git commit -m "Bump version to $version"
    
    # Crea tag
    git tag -a "v$version" -m "$changelog_message"
    
    echo -e "${GREEN}Version $version created successfully!${NC}"
    read -p "Press Enter to continue..."
    show_menu
}

# Funzione per ripristinare da un backup
restore_from_backup() {
    clear
    echo -e "${BLUE}Restore from backup${NC}"
    
    # Elenca i backup disponibili
    echo -e "${YELLOW}Available backups:${NC}"
    ls -1 "$BACKUP_DIR" | grep -E "^${PROJECT_NAME}_.*\.tar\.gz$" | cat -n
    
    echo -e "${YELLOW}Enter the number of the backup to restore (0 to cancel):${NC}"
    read backup_number
    
    if [ "$backup_number" = "0" ]; then
        show_menu
        return
    fi
    
    backup_file=$(ls -1 "$BACKUP_DIR" | grep -E "^${PROJECT_NAME}_.*\.tar\.gz$" | sed -n "${backup_number}p")
    
    if [ -z "$backup_file" ]; then
        echo -e "${RED}Invalid backup selection.${NC}"
        read -p "Press Enter to continue..."
        show_menu
        return
    fi
    
    echo -e "${RED}WARNING: This will overwrite your current project files and database.${NC}"
    echo -e "${RED}Are you sure you want to proceed? (type 'yes' to confirm)${NC}"
    read confirmation
    
    if [ "$confirmation" != "yes" ]; then
        echo -e "${YELLOW}Restore cancelled.${NC}"
        read -p "Press Enter to continue..."
        show_menu
        return
    fi
    
    echo -e "${YELLOW}Restoring from backup ${backup_file}...${NC}"
    
    # Estrai il backup
    tmp_dir="/tmp/restore_${PROJECT_NAME}_$(date +%s)"
    mkdir -p "$tmp_dir"
    tar -xzf "${BACKUP_DIR}/${backup_file}" -C "$tmp_dir"
    
    backup_dir=$(ls -d "$tmp_dir"/*/ | head -1)
    
    # Ripristina il codice
    echo -e "${YELLOW}Restoring code...${NC}"
    rsync -a "${backup_dir}code/" ./
    
    # Ripristina il database
    echo -e "${YELLOW}Restoring database...${NC}"
    if [ -z "$DB_PASSWORD" ]; then
        mysql -u "$DB_USER" "$DB_NAME" < "${backup_dir}database.sql"
    else
        mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < "${backup_dir}database.sql"
    fi
    
    # Ripristina il file .env se necessario
    if [ ! -f ".env" ]; then
        echo -e "${YELLOW}Restoring .env file...${NC}"
        cp "${backup_dir}.env.backup" .env
    fi
    
    # Pulisci
    rm -rf "$tmp_dir"
    
    echo -e "${GREEN}Restore completed successfully!${NC}"
    echo -e "${YELLOW}You may need to run:${NC}"
    echo -e "${YELLOW}   - composer install${NC}"
    echo -e "${YELLOW}   - npm install${NC}"
    echo -e "${YELLOW}   - php artisan migrate${NC}"
    
    read -p "Press Enter to continue..."
    show_menu
}

# Funzione per visualizzare la cronologia delle versioni
view_version_history() {
    clear
    echo -e "${BLUE}Version History${NC}"
    
    if [ -f "CHANGELOG.md" ]; then
        echo -e "${YELLOW}Changelog:${NC}"
        cat CHANGELOG.md
    else
        echo -e "${YELLOW}Git commit history:${NC}"
        git log --pretty=format:"%h %ad | %s%d [%an]" --graph --date=short
    fi
    
    read -p "Press Enter to continue..."
    show_menu
}

# Funzione per compilare gli asset
build_assets() {
    clear
    echo -e "${BLUE}Building assets...${NC}"
    
    echo -e "${YELLOW}Choose build type:${NC}"
    echo -e "${YELLOW}1. Development${NC}"
    echo -e "${YELLOW}2. Production${NC}"
    read -p "Enter your choice [1-2]: " build_choice
    
    case $build_choice in
        1) 
            echo -e "${YELLOW}Building assets for development...${NC}"
            npm run dev
            ;;
        2) 
            echo -e "${YELLOW}Building assets for production...${NC}"
            npm run build
            ;;
        *) 
            echo -e "${RED}Invalid choice.${NC}"
            ;;
    esac
    
    echo -e "${GREEN}Assets built successfully!${NC}"
    read -p "Press Enter to continue..."
    show_menu
}

# Funzione per eseguire i test
run_tests() {
    clear
    echo -e "${BLUE}Running tests...${NC}"
    
    php artisan test
    
    read -p "Press Enter to continue..."
    show_menu
}

# ==== ESECUZIONE PRINCIPALE ====
# Verifica se è un progetto Laravel
if [ ! -f "artisan" ]; then
    echo -e "${RED}This doesn't appear to be a Laravel project. Artisan not found.${NC}"
    exit 1
fi

# Verifica se Git è inizializzato
if [ ! -d ".git" ]; then
    echo -e "${YELLOW}Git repository not found. Would you like to initialize it? (y/n)${NC}"
    read init_git
    if [[ $init_git == "y" ]]; then
        git init
        echo -e "${GREEN}Git repository initialized.${NC}"
        setup_git
    fi
else
    # Setup Git se è già inizializzato
    setup_git
fi

# Inizia con il menu principale
show_menu