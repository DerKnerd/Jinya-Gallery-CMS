package main

import (
	"net/http"
	"os"
)

func main() {
	host := "https://releases.jinya.de/cms/push/"
	if contains(os.Args, "-debug") {
		host = "http://localhost:8090/cms/push/"
	}
	file, err := os.Open("jinya-cms.zip")
	if err != nil {
		panic(err)
	}
	req, err := http.NewRequest(http.MethodPost, host+os.Getenv("TAG_NAME"), file)
	if err != nil {
		panic(err)
	}

	req.Header.Set("JinyaAuthKey", os.Getenv("JINYA_RELEASES_AUTH"))
	_, err = http.DefaultClient.Do(req)

	if err != nil {
		panic(err)
	}
}

func contains(s []string, e string) bool {
	for _, a := range s {
		if a == e {
			return true
		}
	}
	return false
}