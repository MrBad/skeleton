all: datepicker.js
BUILD = build

datepicker.js:
	cat  Source/Locale.en-US.DatePicker.js Source/Picker.js Source/Picker.Attach.js Source/Picker.Date.js > datepicker.js

compact: datepicker.js
	cat $(BUILD)/all.js | uglifyjs > $(BUILD)/compact.js

clean:
	rm datepicker.js