#
# @desc: this is a test shell for branch
#

function logger() {
    local message="$@"
    echo "[$(date +\"%F %T\")] $message"
}
