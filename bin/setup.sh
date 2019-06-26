#!/usr/bin/env bash

# =======================================================================
# Script to setup Charitable in your WordPress installation.
#
# This is designed to be useful for developers who want to quickly spin
# up a test site with Charitable's pages and settings pre-configured.
# =======================================================================

# =======================================================================
# Get the current directory
# =======================================================================
SOURCE="${BASH_SOURCE[0]}"

# While $SOURCE is a symlink, resolve it
while [ -h "$SOURCE" ]; do
    DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"
    SOURCE="$( readlink "$SOURCE" )"
    # If $SOURCE was a relative symlink (so no "/" as prefix, need to resolve it relative to the symlink base directory
    [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE"
done
DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"

# =======================================================================
# User input.
# =======================================================================
allowroot=""
campaigns=0
donations=0

while [ $# -gt 0 ]; do
    case "$1" in
        --allow-root)
            allowroot="--allow-root"
            ;;
        --campaigns=*)
            campaigns="${1#*=}"
            ;;
        --donations=*)
            donations="${1#*=}"
            ;;
        *)
            printf "***************************\n"
            printf "* Error: Invalid argument.*\n"
            printf "***************************\n"
            exit 1
        esac
    shift
done


# =======================================================================
# Prevent root from proceeding unless allowroot is turned on.
# =======================================================================
if [ $(whoami) = "root" ] && [ -z $allowroot ]; then
    printf "It looks like you're running this as root."
    printf "If you'd like to run it as the user that this site is under, you can run the following to become the respective user:\n\n"
    printf "sudo -u USER -i -- bin/setup.sh <command>\n\n"
    printf "If you'd like to continue as root, please run this again, adding this flag: --allow-root\n\n"
    exit 1
fi

# =======================================================================
# Activate Charitable.
# =======================================================================
wp plugin activate charitable $allowroot

# =======================================================================
# Setting up pages.
# =======================================================================
printf "Creating Charitable pages...\n"
profilepage=`wp post create --post_type=page --post_title="Profile" --post_content="[charitable_profile]" --post_status="publish" --porcelain $allowroot`
loginpage=`wp post create --post_type=page --post_title="Login" --post_content="[charitable_login]" --post_status="publish" --porcelain $allowroot`
registrationpage=`wp post create --post_type=page --post_title="Register" --post_content="[charitable_registration]" --post_status="publish" --porcelain $allowroot`
submissionpage=`wp post create --post_type=page --post_title="Create a Campaign" --post_content="[charitable_submit_campaign]" --post_status="publish" --porcelain $allowroot`
wp post create --post_type=page --post_title="Campaigns" --post_content="[campaigns]" --post_status="publish" $allowroot
wp post create --post_type=page --post_title="My Donations" --post_content="[charitable_my_donations]" --post_status="publish" $allowroot
wp post create --post_type=page --post_title="My Campaigns" --post_content="[charitable_my_campaigns]" --post_status="publish" $allowroot
wp post create --post_type=page --post_title="Donations Received" --post_content="[charitable_creator_donations]" --post_status="publish" $allowroot

# =======================================================================
# Updating settings.
# =======================================================================
printf "Pages added. Now updating Charitable settings...\n"
wp option get charitable_settings --format=json $allowroot | php -r "
    \$option = json_decode( fgets(STDIN) );
    if ( ! is_object(\$option) ) { \$option = new stdClass(); }
    \$option->login_page = \"$loginpage\";
    \$option->profile_page = \"$profilepage\";
    \$option->registration_page = \"$registrationpage\";
    \$option->campaign_submission_page = \"$submissionpage\";
    print json_encode(\$option);
" | wp option set charitable_settings --format=json $allowroot

# =======================================================================
# Spawning campaigns & donations.
# =======================================================================
if [ $campaigns -eq 0 ]; then
    printf "Finished creating pages & updated settings.\n"
    exit 1
fi

printf "Creating fake campaigns & donations.\n"
wp eval-file $DIR/spawn-data.php $donations $campaigns $allowroot

printf "All done!"
