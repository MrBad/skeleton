all: compact
BUILD = build

all.js:
	cat app.js  > $(BUILD)/all.js
	cat $(BUILD)/all.js | uglifyjs > $(BUILD)/all.compact.js

compact: all.js
	cat $(BUILD)/all.js | uglifyjs > $(BUILD)/compact.js

clean:
	rm $(BUILD)/*.js