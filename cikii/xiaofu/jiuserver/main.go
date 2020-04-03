package main

import (
	"flag"
	"fmt"
	"log"
	"net/http"

	"go.mongodb.org/mongo-driver/bson/primitive"
	"icode.cikii.com/cikii/xiaofu/jiuserver/config"
	"icode.cikii.com/cikii/xiaofu/jiuserver/models"
	"icode.cikii.com/cikii/xiaofu/jiuserver/routers"
)

var (
	configFile string
)

func main() {
	flag.StringVar(&configFile, "c", "", "Configure File")
	flag.Parse()
	c, err := config.DefaultConfig()
	if err != nil {
		log.Fatalf("Error loading  default config")
	}
	if configFile != "" {
		c, err = config.InitConfigFromFile(configFile)
		if err != nil {
			log.Fatalf("Error loading config: %s", configFile)
		}
	}
	log.Println(c)

	models.InitDB(c)

	tag := models.Tag{}
	tag.ID = primitive.NewObjectID()
	tag.Name = "classic"

	allPost, err := models.FindAllPost()
	if err != nil {
		log.Fatal(err)
	}
	for index, item := range allPost {
		log.Printf("index %d\t", index)
		log.Printf("value %v \n", item)
	}

	router := routers.InitRouter()

	s := &http.Server{
		Addr:         fmt.Sprintf(":%d", c.Server.Port),
		Handler:      router,
		ReadTimeout:  c.Server.ReadTimeout,
		WriteTimeout: c.Server.ReadTimeout,
	}
	s.ListenAndServe()
	// router.Run()
}
