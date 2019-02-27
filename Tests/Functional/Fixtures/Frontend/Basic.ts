config {
	no_cache = 1
	debug = 0
	xhtml_cleaning = 0
	admPanel = 0
	disableAllHeaderCode = 1
	sendCacheHeaders = 0
	sys_language_uid = 0
	sys_language_mode = ignore
	sys_language_overlay = 1
	absRefPrefix = /
	linkVars = L
	contentObjectExceptionHandler = 0

	spamProtectEmailAddresses = 2
	spamProtectEmailAddresses_atSubst = (AT)
	spamProtectEmailAddresses_lastDotSubst = (DOT)

	intTarget = _blank
}

lib.link = TEXT
lib.link {
	value = link
	typolink {
		parameter = 1
	}
}
lib.foo = TEXT
lib.foo.value = FOO

lib.fluid = FLUIDTEMPLATE
lib.fluid.file = EXT:typoscript_rendering/Tests/Functional/Fixtures/Frontend/Template.html

lib.viewHelper = FLUIDTEMPLATE
lib.viewHelper.file = EXT:typoscript_rendering/Tests/Functional/Fixtures/Frontend/ViewHelperTemplate.html

lib.linkViewHelper = FLUIDTEMPLATE
lib.linkViewHelper.file = EXT:typoscript_rendering/Tests/Functional/Fixtures/Frontend/LinkViewHelperTemplate.html

lib.cObjectUriViewHelper = FLUIDTEMPLATE
lib.cObjectUriViewHelper.file = EXT:typoscript_rendering/Tests/Functional/Fixtures/Frontend/CObjectUriViewHelperTemplate.html

lib.cObjectLinkViewHelper = FLUIDTEMPLATE
lib.cObjectLinkViewHelper.file = EXT:typoscript_rendering/Tests/Functional/Fixtures/Frontend/CObjectLinkViewHelperTemplate.html

lib.oldViewHelper = FLUIDTEMPLATE
lib.oldViewHelper.file = EXT:typoscript_rendering/Tests/Functional/Fixtures/Frontend/OldViewHelperTemplate.html

tt_content.typoscriptrendering_plugintest.20 = TEXT

page = PAGE
page {
	10 = TEXT
	10.typolink {
		parameter = 1
		additionalParams.wrap = &tx_typoscriptrendering[context]={"record":"pages_1","path": "|"}
		additionalParams.data = GP:path
		useCacheHash = 1
		returnLast = url
	}
}


[globalVar = GP:L = 1]
config.sys_language_uid = 1
[end]
