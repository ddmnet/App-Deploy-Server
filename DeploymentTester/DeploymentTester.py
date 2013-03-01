import requests
import sys

def deploy( deployment_url, bundleName = "TestBundle", version = "1.0"):
	bundleName = "TestBundle"
	version = "1.0"
	print "Requesting create from server %s" % (deployment_url,)
	r = requests.post(deployment_url, data = dict(formAction="create", bundleName=bundleName, version=version))

	print r.text
	plist_url = r.json()['url']
	print "PLIST URL IS:", plist_url


	data = dict(formAction="upload", bundleName=bundleName)
	files = {'plist_file': ("TestBundle.plist", "THIS IS THE PLIST FILE\n"), 'ipa_file': ("TestBundle.ipa", "THIS IS THE IPA FILE\n")}
	print "Sending files..."
	r = requests.post(deployment_url, files=files, data=data)
	print r.text
	print "Parsed response is:", r.json()

	r = requests.get(plist_url)
	if r.status_code != 200:
		print "\033[91mFailed to get plist file from server.\033[0m"
	else:
		print "\033[92mOK\033[0m"

if len(sys.argv) <= 1:
	print "Usage:\n\tpython DeploymentTester.py [deployment_url]"
else:
	deploy( sys.argv[1] )
