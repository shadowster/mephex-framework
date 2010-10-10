<?php



class Mephex_App_AutoLoaderTest
extends Mephex_Test_TestCase
{
	protected $_auto_loader;
	
	protected $_prev_auto_loader;
	
	
	
	protected function setUp()
	{
		parent::setUp();
		
		$this->_auto_loader	= new Mephex_App_AutoLoader();
		$this->_auto_loader->addClassLoader(new Mephex_App_ClassLoader_PathOriented('Stub_Mephex_App_AutoLoader_PrefixA'));
		$this->_auto_loader->addClassLoader(new Mephex_App_ClassLoader_PathOriented('Stub_Mephex_App_AutoLoader_PrefixB'));
		
		// load Stub_Mephex_App_AutoLoader before we lose the autoloader  
		spl_autoload_call('Stub_Mephex_App_AutoLoader');
		
		$this->_prev_auto_loader	= Mephex_App_AutoLoader::getInstance();
		$this->_prev_auto_loader->unregisterSpl();
		Stub_Mephex_App_AutoLoader::clearInstance();
	}
	
	
	
	protected function tearDown()
	{
		parent::tearDown();
		
		$this->_auto_loader->unregisterSpl();

		Stub_Mephex_App_AutoLoader::restoreInstance($this->_prev_auto_loader);
		$this->_prev_auto_loader->registerSpl();
	}
	
	
	
	public function testGetInstanceReturnsAutoLoaderInstance()
	{
		$this->assertTrue(Mephex_App_AutoLoader::getInstance() instanceof Mephex_App_AutoLoader);
	}
	
	
	
	public function testGetInstanceConsistentlyReturnsSameInstance()
	{
		$this->assertTrue(Mephex_App_AutoLoader::getInstance() === Mephex_App_AutoLoader::getInstance());
	}
	
	
	
	public function testAutoLoaderCanLoadClass()
	{
		$this->assertFalse(class_exists('Stub_Mephex_App_AutoLoader_PrefixA1', false));
		$this->_auto_loader->loadClass('Stub_Mephex_App_AutoLoader_PrefixA1');
		$this->assertTrue(class_exists('Stub_Mephex_App_AutoLoader_PrefixA1', false));
	}
	
	
	
	public function testAutoLoaderCanUseSecondaryAutoLoader()
	{
		$this->assertFalse(class_exists('Stub_Mephex_App_AutoLoader_PrefixB1', false));
		$this->_auto_loader->loadClass('Stub_Mephex_App_AutoLoader_PrefixB1');
		$this->assertTrue(class_exists('Stub_Mephex_App_AutoLoader_PrefixB1', false));
	}
	
	
	
	public function testAutoLoaderCanRegisterItselfWithSpl()
	{
		$this->_auto_loader	= new Mephex_App_AutoLoader();
		$this->_auto_loader->addClassLoader(new Mephex_App_ClassLoader_PathOriented('Stub_Mephex_App_AutoLoader_PrefixA'));
		$this->_auto_loader->addClassLoader(new Mephex_App_ClassLoader_PathOriented('Stub_Mephex_App_AutoLoader_PrefixB'));
		
		$this->assertFalse(class_exists('Stub_Mephex_App_AutoLoader_PrefixA2', false));
		$this->assertFalse(class_exists('Stub_Mephex_App_AutoLoader_PrefixA2', true));
		$this->_auto_loader->registerSpl();
		$this->assertTrue(class_exists('Stub_Mephex_App_AutoLoader_PrefixA2', true));
	}
	
	
	
	public function testAutoLoaderCanUnregisterItselfWithSpl()
	{
		$this->_auto_loader	= new Mephex_App_AutoLoader();
		$this->_auto_loader->registerSpl();
		$this->_auto_loader->addClassLoader(new Mephex_App_ClassLoader_PathOriented('Stub_Mephex_App_AutoLoader_PrefixA'));
		$this->_auto_loader->addClassLoader(new Mephex_App_ClassLoader_PathOriented('Stub_Mephex_App_AutoLoader_PrefixB'));
		
		$this->assertFalse(class_exists('Stub_Mephex_App_AutoLoader_PrefixA4', false));
		$this->assertTrue(class_exists('Stub_Mephex_App_AutoLoader_PrefixA4', true));
		
		$this->_auto_loader->unregisterSpl();
		
		$this->assertFalse(class_exists('Stub_Mephex_App_AutoLoader_PrefixA5', false));
		$this->assertFalse(class_exists('Stub_Mephex_App_AutoLoader_PrefixA5', true));
	}
}