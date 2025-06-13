#!/bin/bash

# Vireo Designs - Auto-Save Control Script
# Easy interface to start/stop/status the automated backup system

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
AUTO_SAVE_SCRIPT="$SCRIPT_DIR/auto-save.sh"
PID_FILE="$SCRIPT_DIR/.auto-save.pid"

# Function to show usage
show_usage() {
    echo -e "${BLUE}🚀 Vireo Designs Auto-Save Control${NC}"
    echo ""
    echo "Usage: $0 {start|stop|status|restart}"
    echo ""
    echo "Commands:"
    echo "  start    - Start the auto-save system"
    echo "  stop     - Stop the auto-save system"
    echo "  status   - Check if auto-save is running"
    echo "  restart  - Restart the auto-save system"
    echo ""
}

# Function to check if auto-save is running
is_running() {
    if [ -f "$PID_FILE" ]; then
        local pid=$(cat "$PID_FILE")
        if ps -p "$pid" > /dev/null 2>&1; then
            return 0
        else
            # Remove stale PID file
            rm -f "$PID_FILE"
            return 1
        fi
    fi
    return 1
}

# Function to start auto-save
start_auto_save() {
    if is_running; then
        echo -e "${YELLOW}⚠️  Auto-save is already running (PID: $(cat "$PID_FILE"))${NC}"
        return 1
    fi
    
    echo -e "${GREEN}🚀 Starting auto-save system...${NC}"
    
    # Start auto-save in background and save PID
    nohup "$AUTO_SAVE_SCRIPT" > "$SCRIPT_DIR/auto-save.log" 2>&1 &
    local pid=$!
    echo "$pid" > "$PID_FILE"
    
    # Give it a moment to start
    sleep 2
    
    if is_running; then
        echo -e "${GREEN}✅ Auto-save started successfully (PID: $pid)${NC}"
        echo -e "${BLUE}📝 Log file: $SCRIPT_DIR/auto-save.log${NC}"
        echo -e "${BLUE}💡 Use '$0 stop' to stop the auto-save system${NC}"
        return 0
    else
        echo -e "${RED}❌ Failed to start auto-save system${NC}"
        rm -f "$PID_FILE"
        return 1
    fi
}

# Function to stop auto-save
stop_auto_save() {
    if ! is_running; then
        echo -e "${YELLOW}⚠️  Auto-save is not running${NC}"
        return 1
    fi
    
    local pid=$(cat "$PID_FILE")
    echo -e "${YELLOW}🛑 Stopping auto-save system (PID: $pid)...${NC}"
    
    # Send SIGTERM for graceful shutdown
    kill -TERM "$pid" 2>/dev/null
    
    # Wait up to 10 seconds for graceful shutdown
    for i in {1..10}; do
        if ! ps -p "$pid" > /dev/null 2>&1; then
            break
        fi
        sleep 1
    done
    
    # Force kill if still running
    if ps -p "$pid" > /dev/null 2>&1; then
        echo -e "${YELLOW}⚠️  Forcing shutdown...${NC}"
        kill -KILL "$pid" 2>/dev/null
    fi
    
    rm -f "$PID_FILE"
    echo -e "${GREEN}✅ Auto-save stopped${NC}"
}

# Function to show status
show_status() {
    echo -e "${BLUE}🔍 Vireo Designs Auto-Save Status${NC}"
    echo ""
    
    if is_running; then
        local pid=$(cat "$PID_FILE")
        echo -e "${GREEN}✅ Auto-save is running (PID: $pid)${NC}"
        
        # Show process info
        echo -e "${BLUE}📊 Process information:${NC}"
        ps -p "$pid" -o pid,ppid,stime,time,cmd 2>/dev/null | tail -n +2
        
        # Show recent log entries
        if [ -f "$SCRIPT_DIR/auto-save.log" ]; then
            echo ""
            echo -e "${BLUE}📝 Recent log entries:${NC}"
            tail -n 5 "$SCRIPT_DIR/auto-save.log"
        fi
        
        # Show backup statistics
        echo ""
        echo -e "${BLUE}📊 Backup statistics:${NC}"
        if [ -f ".git/backup.log" ]; then
            local backup_count=$(wc -l < .git/backup.log)
            echo -e "${BLUE}  Total backups created: $backup_count${NC}"
            echo -e "${BLUE}  Latest backup:${NC}"
            tail -n 1 .git/backup.log 2>/dev/null | sed 's/^/    /'
        fi
        
    else
        echo -e "${YELLOW}⚠️  Auto-save is not running${NC}"
        
        # Check for log file
        if [ -f "$SCRIPT_DIR/auto-save.log" ]; then
            echo ""
            echo -e "${BLUE}📝 Last log entries:${NC}"
            tail -n 3 "$SCRIPT_DIR/auto-save.log"
        fi
    fi
}

# Function to restart auto-save
restart_auto_save() {
    echo -e "${BLUE}🔄 Restarting auto-save system...${NC}"
    stop_auto_save
    sleep 2
    start_auto_save
}

# Main script logic
case "$1" in
    start)
        start_auto_save
        ;;
    stop)
        stop_auto_save
        ;;
    status)
        show_status
        ;;
    restart)
        restart_auto_save
        ;;
    *)
        show_usage
        exit 1
        ;;
esac

exit $?