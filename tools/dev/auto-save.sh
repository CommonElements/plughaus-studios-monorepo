#!/bin/bash

# Vireo Designs - Automated Development Save Script
# Periodically saves work in progress with timestamped commits

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
SAVE_INTERVAL=1800  # 30 minutes in seconds
MAX_SAVES_PER_SESSION=20
WIP_BRANCH_PREFIX="wip"

echo -e "${GREEN}🚀 Vireo Designs Auto-Save System Starting${NC}"
echo -e "${BLUE}📝 Save interval: $((SAVE_INTERVAL / 60)) minutes${NC}"
echo -e "${BLUE}📊 Max saves per session: $MAX_SAVES_PER_SESSION${NC}"

# Function to check if we're in a git repository
check_git_repo() {
    if ! git rev-parse --git-dir > /dev/null 2>&1; then
        echo -e "${RED}❌ Error: Not in a git repository${NC}"
        exit 1
    fi
}

# Function to get current branch
get_current_branch() {
    git rev-parse --abbrev-ref HEAD
}

# Function to check if there are changes to save
has_changes() {
    # Check for staged changes
    if ! git diff --cached --quiet; then
        return 0
    fi
    
    # Check for unstaged changes
    if ! git diff --quiet; then
        return 0
    fi
    
    # Check for untracked files (exclude common temp files)
    untracked=$(git ls-files --others --exclude-standard | grep -v -E '\.(tmp|log|cache)$' | wc -l)
    if [ "$untracked" -gt 0 ]; then
        return 0
    fi
    
    return 1
}

# Function to create auto-save commit
create_auto_save() {
    local timestamp=$(date +"%Y-%m-%d %H:%M:%S")
    local current_branch=$(get_current_branch)
    
    echo -e "${YELLOW}💾 Creating auto-save at $timestamp${NC}"
    
    # Stage all changes (including untracked files)
    git add .
    
    # Create commit with auto-save message
    git commit -m "🔄 AUTO-SAVE: Development checkpoint

📅 Timestamp: $timestamp
🌿 Branch: $current_branch
🤖 Automated development backup

This is an automatic save created during development.
Contains work in progress that may not be complete.

🤖 Generated with [Claude Code](https://claude.ai/code)

Co-Authored-By: Claude <noreply@anthropic.com>"

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Auto-save commit created successfully${NC}"
        return 0
    else
        echo -e "${RED}❌ Failed to create auto-save commit${NC}"
        return 1
    fi
}

# Function to clean up old auto-save commits
cleanup_auto_saves() {
    local current_branch=$(get_current_branch)
    
    # Count auto-save commits in current session
    auto_save_count=$(git log --oneline --grep="AUTO-SAVE" --since="1 day ago" | wc -l)
    
    if [ "$auto_save_count" -gt "$MAX_SAVES_PER_SESSION" ]; then
        echo -e "${YELLOW}🧹 Cleaning up old auto-save commits (keeping last $MAX_SAVES_PER_SESSION)${NC}"
        
        # Note: In a real scenario, you might want to squash or remove old auto-save commits
        # For safety, we'll just warn about the count
        echo -e "${YELLOW}⚠️  Found $auto_save_count auto-save commits in the last 24 hours${NC}"
        echo -e "${YELLOW}💡 Consider squashing old auto-save commits before pushing${NC}"
    fi
}

# Function to handle graceful shutdown
cleanup_and_exit() {
    echo -e "\n${YELLOW}🛑 Auto-save system shutting down...${NC}"
    
    # Check if there are unsaved changes
    if has_changes; then
        echo -e "${YELLOW}💾 Unsaved changes detected. Creating final auto-save...${NC}"
        create_auto_save
    fi
    
    echo -e "${GREEN}✅ Auto-save system stopped${NC}"
    exit 0
}

# Set up signal handlers for graceful shutdown
trap cleanup_and_exit SIGINT SIGTERM

# Verify we're in a git repository
check_git_repo

# Get initial branch
initial_branch=$(get_current_branch)
save_count=0

echo -e "${GREEN}🎯 Auto-save system active on branch: $initial_branch${NC}"
echo -e "${BLUE}💡 Press Ctrl+C to stop and create final save${NC}"
echo ""

# Main auto-save loop
while true; do
    # Check if branch has changed
    current_branch=$(get_current_branch)
    if [ "$current_branch" != "$initial_branch" ]; then
        echo -e "${BLUE}🌿 Branch changed from '$initial_branch' to '$current_branch'${NC}"
        initial_branch="$current_branch"
    fi
    
    # Skip auto-save if on main branch
    if [ "$current_branch" = "main" ]; then
        echo -e "${YELLOW}⚠️  Skipping auto-save on main branch${NC}"
        sleep "$SAVE_INTERVAL"
        continue
    fi
    
    # Check for changes and create auto-save if needed
    if has_changes; then
        create_auto_save
        
        # Increment save count and check for cleanup
        save_count=$((save_count + 1))
        cleanup_auto_saves
        
        echo -e "${BLUE}📊 Session saves: $save_count${NC}"
        
        # Check if we've reached max saves
        if [ "$save_count" -ge "$MAX_SAVES_PER_SESSION" ]; then
            echo -e "${YELLOW}⚠️  Reached maximum saves per session ($MAX_SAVES_PER_SESSION)${NC}"
            echo -e "${YELLOW}💡 Consider committing your changes and restarting the auto-save${NC}"
        fi
    else
        echo -e "${GREEN}✅ No changes to save at $(date +"%H:%M:%S")${NC}"
    fi
    
    # Wait for next save interval
    echo -e "${BLUE}⏰ Next auto-save in $((SAVE_INTERVAL / 60)) minutes...${NC}"
    sleep "$SAVE_INTERVAL"
done