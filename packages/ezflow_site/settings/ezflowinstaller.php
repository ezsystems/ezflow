<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Flow
// SOFTWARE RELEASE: 1.1.0
// COPYRIGHT NOTICE: Copyright (C) 1999-2008 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//
class eZFlownInstaller extends eZSiteInstaller
{
    const MAJOR_VERSION = 2.0;
    const MINOR_VERSION = 0;

    function eZFlownInstaller( $parameters = false )
    {
        eZSiteInstaller::eZSiteInstaller( $parameters );
    }

    function &instance( $params )
    {
        $impl = & $GLOBALS["eZFlownInstallerGlobalInstance"];
        if ( get_class( $impl ) != "eZFlownInstaller" )
            $impl = new eZFlownInstaller( $params );
        return $impl;
    }

    function resetGlobals()
    {
        unset( $GLOBALS["eZFlownInstallerGlobalInstance"] );
    }

    function initSettings( $parameters )
    {
        $siteINI = eZINI::instance();
        $classIdentifier = 'template_look';
        //get the class
        $class = eZContentClass::fetchByIdentifier( $classIdentifier, true, eZContentClass::VERSION_STATUS_TEMPORARY );
        if ( ! $class )
        {
            $class = eZContentClass::fetchByIdentifier( $classIdentifier, true, eZContentClass::VERSION_STATUS_DEFINED );
            if ( ! $class )
            {
                eZDebug::writeError( "Warning, DEFINED version for class identifier $classIdentifier does not exist." );
                return;
            }
        }
        $classId = $class->attribute( 'id' );
        $this->Settings['template_look_class_id'] = $classId;
        $objects = eZContentObject::fetchSameClassList( $classId );
        if ( ! count( $objects ) )
        {
            eZDebug::writeError( "Object of class $classIdentifier does not exist." );
            return;
        }
        $templateLookObject = $objects[0];
        $this->Settings['template_look_object'] = $templateLookObject;
        $this->Settings['template_look_object_id'] = $templateLookObject->attribute( 'id' );
        if ( ! is_array( $parameters ) )
            return;
        $this->addSetting( 'admin_account_id', eZSiteInstaller::getParam( $parameters, 'object_remote_map/1bb4fe25487f05527efa8bfd394cecc7', '' ) );
        $this->addSetting( 'guest_accounts_id', eZSiteInstaller::getParam( $parameters, 'object_remote_map/5f7f0bdb3381d6a461d8c29ff53d908f', '' ) );
        $this->addSetting( 'anonymous_accounts_id', eZSiteInstaller::getParam( $parameters, 'object_remote_map/15b256dbea2ae72418ff5facc999e8f9', '' ) );
        $this->addSetting( 'package_object', eZSiteInstaller::getParam( $parameters, 'package_object', false ) );
        $this->addSetting( 'design_list', eZSiteInstaller::getParam( $parameters, 'design_list', array() ) );
        $this->addSetting( 'main_site_design', strtolower( $this->solutionName() ) );
        $this->addSetting( 'extension_list', array( 
            'ezjscore', 
            'ezstarrating', 
            'ezgmaplocation', 
            strtolower( $this->solutionName() ), 
            'ezwt', 
            'ezwebin' 
        ) );
        $this->addSetting( 'version', $this->solutionVersion() );
        $this->addSetting( 'locales', eZSiteInstaller::getParam( $parameters, 'all_language_codes', array() ) );
        // usual user siteaccess like 'ezwebin_site'
        $this->addSetting( 'user_siteaccess', eZSiteInstaller::getParam( $parameters, 'user_siteaccess', '' ) );
        // usual admin siteaccess like 'ezwebin_site_admin'
        $this->addSetting( 'admin_siteaccess', eZSiteInstaller::getParam( $parameters, 'admin_siteaccess', '' ) );
        // extra siteaccess based on languages info, like 'eng', 'rus', ...
        $this->addSetting( 'language_based_siteaccess_list', $this->languageNameListFromLocaleList( $this->setting( 'locales' ) ) );
        $this->addSetting( 'user_siteaccess_list', array_merge( array( 
            $this->setting( 'user_siteaccess' ) 
        ), $this->setting( 'language_based_siteaccess_list' ) ) );
        $this->addSetting( 'all_siteaccess_list', array_merge( $this->setting( 'user_siteaccess_list' ), array( 
            $this->setting( 'admin_siteaccess' ), 
            'iphone' 
        ) ) );
        $this->addSetting( 'access_type', eZSiteInstaller::getParam( $parameters, 'site_type/access_type', '' ) );
        $this->addSetting( 'access_type_value', eZSiteInstaller::getParam( $parameters, 'site_type/access_type_value', '' ) );
        $this->addSetting( 'admin_access_type_value', eZSiteInstaller::getParam( $parameters, 'site_type/admin_access_type_value', '' ) );
        $this->addSetting( 'host', eZSiteInstaller::getParam( $parameters, 'host', '' ) );
        $siteaccessUrls = array( 
            'admin' => $this->createSiteaccessUrls( array( 
                'siteaccess_list' => array( 
                    $this->setting( 'admin_siteaccess' ) 
                ), 
                'access_type' => $this->setting( 'access_type' ), 
                'access_type_value' => $this->setting( 'admin_access_type_value' ), 
                'host' => $this->setting( 'host' ), 
                'host_prepend_siteaccess' => false
            ) ), 
            'user' => $this->createSiteaccessUrls( array( 
                'siteaccess_list' => array( 
                    $this->setting( 'user_siteaccess' ) 
                ), 
                'access_type' => $this->setting( 'access_type' ), 
                'access_type_value' => $this->setting( 'access_type_value' ), 
                'host' => $this->setting( 'host' ), 
                'host_prepend_siteaccess' => false
            ) ), 
            'translation' => $this->createSiteaccessUrls( array( 
                'siteaccess_list' => $this->setting( 'language_based_siteaccess_list' ), 
                'access_type' => $this->setting( 'access_type' ), 
                'access_type_value' => $this->setting( 'access_type_value' ) + 1,  // 'access_type_value' is for 'ezwein_site_user', so take next port number.
                'host' => $this->setting( 'host' ), 
                'exclude_port_list' => array( 
                    $this->setting( 'admin_access_type_value' ), 
                    $this->setting( 'access_type_value' ) 
                ) 
            ) ) 
        );
        $this->addSetting( 'siteaccess_urls', $siteaccessUrls );
        $this->addSetting( 'primary_language', eZSiteInstaller::getParam( $parameters, 'all_language_codes/0', '' ) );
        $this->addSetting( 'var_dir', eZSiteInstaller::getParam( $parameters, 'var_dir', 'var/' . $this->setting( 'user_siteaccess' ) ) );
    }

    function initSteps()
    {
        $postInstallSteps = array( 
            array( 
                '_function' => 'dbBegin', 
                '_params' => array() 
            ), 
            array( 
                '_function' => 'setVersion', 
                '_params' => array() 
            ), 
            array( 
                '_function' => 'postInstallAdminSiteaccessINIUpdate', 
                '_params' => array() 
            ), 
            array( 
                '_function' => 'postInstallUserSiteaccessINIUpdate', 
                '_params' => array() 
            ), 
            array( 
                '_function' => 'createTranslationSiteAccesses', 
                '_params' => array() 
            ), 
            array( 
                '_function' => 'createiPhoneSiteAccess', 
                '_params' => array() 
            ), 
            array( 
                '_function' => 'updateiPhoneImageINISettings', 
                '_params' => array() 
            ), 
            array( 
                '_function' => 'updateTemplateLookClassAttributes', 
                '_params' => array() 
            ), 
            array( 
                '_function' => 'updateTemplateLookObjectAttributes', 
                '_params' => array() 
            ), 
            array( 
                '_function' => 'swapNodes', 
                '_params' => array( 
                    'src_node' => array( 
                        'name' => "eZ Publish" 
                    ), 
                    'dst_node' => array( 
                        'name' => "Home" 
                    ) 
                ) 
            ), 
            array( 
                '_function' => 'removeContentObject', 
                '_params' => array( 
                    'name' => 'eZ Publish' 
                ) 
            ), 
            array( 
                '_function' => 'removeClassAttribute', 
                '_params' => array( 
                    'class_id' => $this->setting( 'template_look_class_id' ), 
                    'attribute_identifier' => 'id' 
                ) 
            ), 
            array( 
                '_function' => 'updateObjectAttributeFromString', 
                '_params' => array( 
                    'object_id' => $this->setting( 'template_look_object_id' ), 
                    'class_attribute_identifier' => 'image', 
                    'string' => array( 
                        '_function' => 'packageFileItemPath', 
                        '_params' => array( 
                            'collection' => 'default', 
                            'file_item' => array( 
                                'type' => 'image', 
                                'name' => 'logo.png' 
                            ) 
                        ) 
                    ) 
                ) 
            ), 
            array( 
                '_function' => 'updateObjectAttributeFromString', 
                '_params' => array( 
                    'object_id' => $this->setting( 'template_look_object_id' ), 
                    'class_attribute_identifier' => 'sitestyle', 
                    'string' => 'ezwebin_design' 
                ) 
            ), 
            array( 
                '_function' => 'createContentSection', 
                '_params' => array( 
                    'name' => 'Restricted', 
                    'navigation_part_identifier' => 'ezcontentnavigationpart' 
                ) 
            ), 
            array( 
                '_function' => 'addPoliciesForRole', 
                '_params' => array( 
                    'role_name' => 'Anonymous', 
                    'policies' => array( 
                        array( 
                            'module' => 'content', 
                            'function' => 'read', 
                            'limitation' => array( 
                                'Class' => array( 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'image' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'banner' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'flash' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'real_video' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'windows_media' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'quicktime' 
                                        ) 
                                    ) 
                                ), 
                                'Section' => array( 
                                    '_function' => 'sectionIDbyName', 
                                    '_params' => array( 
                                        'section_name' => 'Media' 
                                    ) 
                                ) 
                            ) 
                        ) 
                    ) 
                ) 
            ), 
            array( 
                '_function' => 'removePoliciesForRole', 
                '_params' => array( 
                    'role_name' => 'Editor', 
                    'policies' => array( 
                        array( 
                            'module' => 'content', 
                            'function' => '*' 
                        ) 
                    ) 
                ) 
            ), 
            array( 
                '_function' => 'addPoliciesForRole', 
                '_params' => array( 
                    'role_name' => 'Editor', 
                    'policies' => array( 
                        array( 
                            'module' => 'content', 
                            'function' => 'create', 
                            'limitation' => array( 
                                'Class' => array( 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'folder' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'link' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'file' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'product' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'feedback_form' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'frontpage' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'article' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'article_mainpage' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'article_subpage' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'blog' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'poll' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'multicalendar' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'documentation_page' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'infobox' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'flash' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'quicktime' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'windows_media' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'real_video' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'gallery' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'forum' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'forums' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'event_calendar' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'banner' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'image' 
                                        ) 
                                    ) 
                                ), 
                                'ParentClass' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'folder' 
                                    ) 
                                ) 
                            ) 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'create', 
                            'limitation' => array( 
                                'Class' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'blog_post' 
                                    ) 
                                ), 
                                'ParentClass' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'blog' 
                                    ) 
                                ) 
                            ) 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'create', 
                            'limitation' => array( 
                                'Class' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'forum_topic' 
                                    ) 
                                ), 
                                'ParentClass' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'forum' 
                                    ) 
                                ) 
                            ) 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'create', 
                            'limitation' => array( 
                                'Class' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'event' 
                                    ) 
                                ), 
                                'ParentClass' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'event_calendar' 
                                    ) 
                                ) 
                            ) 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'create', 
                            'limitation' => array( 
                                'Class' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'image' 
                                    ) 
                                ), 
                                'ParentClass' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'gallery' 
                                    ) 
                                ) 
                            ) 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'create', 
                            'limitation' => array( 
                                'Class' => array( 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'folder' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'link' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'feedback_form' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'frontpage' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'documentation_page' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'gallery' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'event_calendar' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'multicalendar' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'forums' 
                                        ) 
                                    ) 
                                ), 
                                'ParentClass' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'frontpage' 
                                    ) 
                                ) 
                            ) 
                        ), 
                        array( 
                            'module' => 'websitetoolbar', 
                            'function' => 'use', 
                            'limitation' => array( 
                                'Class' => array( 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'folder' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'link' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'article' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'article_mainpage' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'article_subpage' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'blog' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'blog_post' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'product' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'feedback_form' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'frontpage' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'documentation_page' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'multicalendar' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'poll' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'file' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'flash' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'image' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'quicktime' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'windows_media' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'real_video' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'gallery' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'forum' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'event' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'event_calendar' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'forums' 
                                        ) 
                                    ) 
                                ) 
                            ) 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'edit' 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'read', 
                            'limitation' => array( 
                                'Section' => array( 
                                    array( 
                                        '_function' => 'sectionIDbyName', 
                                        '_params' => array( 
                                            'section_name' => 'Standard' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'sectionIDbyName', 
                                        '_params' => array( 
                                            'section_name' => 'Restricted' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'sectionIDbyName', 
                                        '_params' => array( 
                                            'section_name' => 'Media' 
                                        ) 
                                    ) 
                                ) 
                            ) 
                        ) 
                    ) 
                ) 
            ), 
            array( 
                '_function' => 'addPoliciesForRole', 
                '_params' => array( 
                    'role_name' => 'Editor', 
                    'policies' => array( 
                        array( 
                            'module' => 'notification', 
                            'function' => 'use' 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'manage_locations' 
                        ), 
                        array( 
                            'module' => 'ezodf', 
                            'function' => '*' 
                        ), 
                        array( 
                            'module' => 'ezflow', 
                            'function' => '*' 
                        ), 
                        array( 
                            'module' => 'ezajax', 
                            'function' => '*' 
                        ), 
                        array( 
                            'module' => 'shop', 
                            'function' => 'administrate' 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'diff' 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'versionread' 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'versionremove' 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'remove' 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'translate' 
                        ), 
                        array( 
                            'module' => 'rss', 
                            'function' => 'feed' 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'bookmark' 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'pendinglist' 
                        ) 
                    ) 
                ) 
            ), 
            array( 
                '_function' => 'createContentObject', 
                '_params' => array( 
                    'class_identifier' => 'user_group', 
                    'location' => 'users', 
                    'attributes' => array( 
                        'name' => 'Partners', 
                        'description' => '' 
                    ) 
                ) 
            ),
/*
                                   array( '_function' => 'setSection',
                                          '_params' => array( 'location' => 'partners',
                                                              'section_name' => 'Restricted' ) ),
*/
                                   array( 
                '_function' => 'addPoliciesForRole', 
                '_params' => array( 
                    'role_name' => 'Partner', 
                    'policies' => array( 
                        array( 
                            'module' => 'content', 
                            'function' => 'read', 
                            'limitation' => array( 
                                'Section' => array( 
                                    '_function' => 'sectionIDbyName', 
                                    '_params' => array( 
                                        'section_name' => 'Restricted' 
                                    ) 
                                ) 
                            ) 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'create', 
                            'limitation' => array( 
                                'Class' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'forum_topic' 
                                    ) 
                                ), 
                                'Section' => array( 
                                    '_function' => 'sectionIDbyName', 
                                    '_params' => array( 
                                        'section_name' => 'Restricted' 
                                    ) 
                                ), 
                                'ParentClass' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'forum' 
                                    ) 
                                ) 
                            ) 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'create', 
                            'limitation' => array( 
                                'Class' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'forum_reply' 
                                    ) 
                                ), 
                                'Section' => array( 
                                    '_function' => 'sectionIDbyName', 
                                    '_params' => array( 
                                        'section_name' => 'Restricted' 
                                    ) 
                                ), 
                                'ParentClass' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'forum_topic' 
                                    ) 
                                ) 
                            ) 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'create', 
                            'limitation' => array( 
                                'Class' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'comment' 
                                    ) 
                                ), 
                                'Section' => array( 
                                    '_function' => 'sectionIDbyName', 
                                    '_params' => array( 
                                        'section_name' => 'Restricted' 
                                    ) 
                                ), 
                                'ParentClass' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'article' 
                                    ) 
                                ) 
                            ) 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'edit', 
                            'limitation' => array( 
                                'Class' => array( 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'comment' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'forum_topic' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'forum_reply' 
                                        ) 
                                    ) 
                                ), 
                                'Section' => array( 
                                    array( 
                                        '_function' => 'sectionIDbyName', 
                                        '_params' => array( 
                                            'section_name' => 'Restricted' 
                                        ) 
                                    ) 
                                ), 
                                'Owner' => 1 
                            ) 
                        ),  // self
                        array( 
                            'module' => 'user', 
                            'function' => 'selfedit' 
                        ), 
                        array( 
                            'module' => 'notification', 
                            'function' => 'use' 
                        ), 
                        array( 
                            'module' => 'shop', 
                            'function' => 'administrate' 
                        ) 
                    ) 
                ) 
            ), 
            array( 
                '_function' => 'renameContentObject', 
                '_params' => array( 
                    'contentobject_id' => '11',  // 11 is id of "Guest accounts"
                    'name' => 'Members' 
                ) 
            ), 
            array( 
                '_function' => 'addPoliciesForRole', 
                '_params' => array( 
                    'role_name' => 'Member', 
                    'policies' => array( 
                        array( 
                            'module' => 'content', 
                            'function' => 'create', 
                            'limitation' => array( 
                                'Class' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'forum_topic' 
                                    ) 
                                ), 
                                'Section' => array( 
                                    '_function' => 'sectionIDbyName', 
                                    '_params' => array( 
                                        'section_name' => 'Standard' 
                                    ) 
                                ), 
                                'ParentClass' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'forum' 
                                    ) 
                                ) 
                            ) 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'create', 
                            'limitation' => array( 
                                'Class' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'forum_reply' 
                                    ) 
                                ), 
                                'Section' => array( 
                                    '_function' => 'sectionIDbyName', 
                                    '_params' => array( 
                                        'section_name' => 'Standard' 
                                    ) 
                                ), 
                                'ParentClass' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'forum_topic' 
                                    ) 
                                ) 
                            ) 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'create', 
                            'limitation' => array( 
                                'Class' => array( 
                                    '_function' => 'classIDbyIdentifier', 
                                    '_params' => array( 
                                        'identifier' => 'comment' 
                                    ) 
                                ), 
                                'Section' => array( 
                                    '_function' => 'sectionIDbyName', 
                                    '_params' => array( 
                                        'section_name' => 'Standard' 
                                    ) 
                                ), 
                                'ParentClass' => array( 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'article' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'blog_post' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'article_mainpage' 
                                        ) 
                                    ) 
                                ) 
                            ) 
                        ), 
                        array( 
                            'module' => 'content', 
                            'function' => 'edit', 
                            'limitation' => array( 
                                'Class' => array( 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'comment' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'forum_topic' 
                                        ) 
                                    ), 
                                    array( 
                                        '_function' => 'classIDbyIdentifier', 
                                        '_params' => array( 
                                            'identifier' => 'forum_reply' 
                                        ) 
                                    ) 
                                ), 
                                'Section' => array( 
                                    array( 
                                        '_function' => 'sectionIDbyName', 
                                        '_params' => array( 
                                            'section_name' => 'Standard' 
                                        ) 
                                    ) 
                                ), 
                                'Owner' => 1 
                            ) 
                        ),  // self
                        array( 
                            'module' => 'user', 
                            'function' => 'selfedit' 
                        ), 
                        array( 
                            'module' => 'notification', 
                            'function' => 'use' 
                        ), 
                        array( 
                            'module' => 'shop', 
                            'function' => 'buy' 
                        ), 
                        array( 
                            'module' => 'user', 
                            'function' => 'password' 
                        ), 
                        array( 
                            'module' => 'ezjscore', 
                            'function' => 'call' 
                        )
                    ) 
                ) 
            ), 
            array( 
                '_function' => 'assignUserToRole', 
                '_params' => array( 
                    'location' => 'users/members', 
                    'role_name' => 'Member' 
                ) 
            ), 
            array( 
                '_function' => 'assignUserToRole', 
                '_params' => array( 
                    'location' => 'users/partners', 
                    'role_name' => 'Partner' 
                ) 
            ), 
            array( 
                '_function' => 'assignUserToRole', 
                '_params' => array( 
                    'location' => 'users/partners', 
                    'role_name' => 'Member' 
                ) 
            ), 
            array( 
                '_function' => 'assignUserToRole', 
                '_params' => array( 
                    'location' => 'users/partners', 
                    'role_name' => 'Anonymous' 
                ) 
            ), 
            array( 
                '_function' => 'assignUserToRole', 
                '_params' => array( 
                    'location' => 'users/editors', 
                    'role_name' => 'Member' 
                ) 
            ), 
            array( 
                '_function' => 'updateClassAttributes', 
                '_params' => array( 
                    'class' => array( 
                        'identifier' => 'folder' 
                    ), 
                    'attributes' => array( 
                        array( 
                            'identifier' => 'short_description', 
                            'new_name' => 'Summary' 
                        ), 
                        array( 
                            'identifier' => 'show_children', 
                            'new_name' => 'Display sub items' 
                        ) 
                    ) 
                ) 
            ), 
            array( 
                '_function' => 'setRSSExport', 
                '_params' => array( 
                    'creator' => '14', 
                    'access_url' => 'my_feed', 
                    'main_node_only' => '1', 
                    'number_of_objects' => '10', 
                    'rss_version' => '2.0', 
                    'status' => '1', 
                    'title' => 'My RSS Feed', 
                    'rss_export_itmes' => array( 
                        0 => array( 
                            'class_id' => '16', 
                            'description' => 'intro', 
                            'source_node_id' => '139', 
                            'status' => '1', 
                            'title' => 'title' 
                        ) 
                    ) 
                ) 
            ), 
            array( 
                '_function' => 'dbCommit', 
                '_params' => array() 
            ) 
        );
        $this->Steps['post_install'] = $postInstallSteps;
    }

    /*!
     Re-impl.
    */
    function handleError()
    {
        $errCode = $this->lastErrorCode();
        if ( $errCode === eZSiteInstaller::ERR_ABORT )
            $this->dbCommit( array() );
        return $errCode;
    }

    /*!
     Install from command-line.
    */
    function install()
    {
        $settings = array();
        $settings[] = array( 
            'settings_dir' => 'settings/siteaccess/' . $this->setting( 'user_siteaccess' ), 
            'groups' => $this->siteINISettings() 
        );
        $settings[] = array( 
            'settings_dir' => 'settings/siteaccess/' . $this->setting( 'admin_siteaccess' ), 
            'groups' => $this->adminINISettings() 
        );
        $settings[] = array( 
            'settings_dir' => 'settings/override', 
            'groups' => $this->commonINISettings() 
        );
        foreach ($settings as $settingsGroup)
            $this->updateINIFiles( $settingsGroup );
        $this->updateRoles( array( 
            'roles' => $this->siteRoles() 
        ) );
        $this->updatePreferences( array( 
            'prefs' => $this->sitePreferences() 
        ) );
        $this->postInstall();
    }

    /*!
      pre-install stuff.
    */
    function preInstall()
    {
        $db = eZDB::instance();
        $db->begin();
        // extend 'folder' class
        $this->addClassAttributes( array( 
            'class' => array( 
                'identifier' => 'folder' 
            ), 
            'attributes' => array( 
                array( 
                    'identifier' => 'tags', 
                    'name' => 'Tags', 
                    'data_type_string' => 'ezkeyword' 
                ), 
                array( 
                    'identifier' => 'publish_date', 
                    'name' => 'Publish date', 
                    'data_type_string' => 'ezdatetime', 
                    'default_value' => 0 
                ) 
            ) 
        ) );
        $db->commit();
        // hack for images/binaryfiles
        // need to set siteaccess to have correct placement(VarDir) for files in SetupWizard
        $ini = eZINI::instance();
        $ini->setVariable( 'FileSettings', 'VarDir', $this->setting( 'var_dir' ) );
        $contentINI = eZINI::instance( 'content.ini' );
        $datatypeRepositories = $contentINI->variable( 'DataTypeSettings', 'ExtensionDirectories' );
        $datatypeRepositories[] = 'ezflow';
        $datatypeRepositories[] = 'ezstarrating';
        $datatypeRepositories[] = 'ezgmaplocation';
        $contentINI->setVariables( array( 
            'DataTypeSettings' => array( 
                'ExtensionDirectories' => $datatypeRepositories 
            ) 
        ) );
        $availableDatatype = $contentINI->variable( 'DataTypeSettings', 'AvailableDataTypes' );
        $availableDatatype[] = 'ezpage';
        $availableDatatype[] = 'ezsrrating';
        $availableDatatype[] = 'ezgmaplocation';
        $contentINI->setVariables( array( 
            'DataTypeSettings' => array( 
                'AvailableDataTypes' => $availableDatatype 
            ) 
        ) );
        $this->insertDBFile( 'ezflow_extension', 'ezflow', true );
        $this->insertDBFile( 'ezstarrating_extension', 'ezstarrating' );
        $this->insertDBFile( 'ezgmaplocation_extension', 'ezgmaplocation' );
    }

    function insertDBFile( $packageName, $extensionName, $loadContent = false )
    {
        $db = eZDB::instance();
        $extensionPackage = eZPackage::fetch( $packageName, false, false, false );
        if ( $extensionPackage instanceof eZPackage )
        {
            switch ($db->databaseName())
            {
                case 'mysql':
                    $sqlFile = 'mysql.sql';
                    $path = $extensionPackage->path() . '/ezextension/' . $extensionName . '/sql/mysql';
                    break;
                case 'postgresql':
                    $sqlFile = 'postgresql.sql';
                    $path = $extensionPackage->path() . '/ezextension/' . $extensionName . '/sql/postgresql';
                    break;
            }
            $res = $db->insertFile( $path, $sqlFile, false );
            if ( ! $res )
            {
                eZDebug::writeError( 'Can\'t initialize ' . $extensionName . ' database shema.', __METHOD__ );
            }
            if ( $res && $loadContent )
            {
                $sqlFile = 'democontent.sql';
                $path = $extensionPackage->path() . '/ezextension/' . $extensionName . '/sql/common';
                $res = $db->insertFile( $path, $sqlFile, false );
                if ( ! $res )
                {
                    eZDebug::writeError( 'Can\'t initialize ' . $extensionName . ' demo data.', __METHOD__ );
                }
            }
        }
    }

    function updateTemplateLookClassAttributes( $params = false )
    {
        $newAttributesInfo = array( 
            array( 
                "data_type_string" => "ezurl", 
                "name" => "Site map URL", 
                "identifier" => "site_map_url" 
            ), 
            array( 
                "data_type_string" => "ezurl", 
                "name" => "Tag Cloud URL", 
                "identifier" => "tag_cloud_url" 
            ), 
            array( 
                "data_type_string" => "ezstring", 
                "name" => "Login (label)", 
                "identifier" => "login_label" 
            ), 
            array( 
                "data_type_string" => "ezstring", 
                "name" => "Logout (label)", 
                "identifier" => "logout_label" 
            ), 
            array( 
                "data_type_string" => "ezstring", 
                "name" => "My profile (label)", 
                "identifier" => "my_profile_label" 
            ), 
            array( 
                "data_type_string" => "ezstring", 
                "name" => "Register new user (label)", 
                "identifier" => "register_user_label" 
            ), 
            array( 
                "data_type_string" => "ezstring", 
                "name" => "RSS feed", 
                "identifier" => "rss_feed" 
            ), 
            array( 
                "data_type_string" => "ezstring", 
                "name" => "Shopping basket (label)", 
                "identifier" => "shopping_basket_label" 
            ), 
            array( 
                "data_type_string" => "ezstring", 
                "name" => "Site settings (label)", 
                "identifier" => "site_settings_label" 
            ), 
            array( 
                "data_type_string" => "eztext", 
                "name" => "Footer text", 
                "identifier" => "footer_text" 
            ), 
            array( 
                "data_type_string" => "ezboolean", 
                "name" => "Hide \"Powered by\"", 
                "identifier" => "hide_powered_by" 
            ), 
            array( 
                "data_type_string" => "eztext", 
                "name" => "Footer Javascript", 
                "identifier" => "footer_script" 
            ) 
        );
        $this->addClassAttributes( array( 
            'class' => array( 
                'id' => $this->setting( 'template_look_class_id' ) 
            ), 
            'attributes' => $newAttributesInfo 
        ) );
    }

    function updateTemplateLookObjectAttributes( $params = false )
    {
        //create data array
        $templateLookData = array( 
            "site_map_url" => array( 
                "DataText" => "Site map", 
                "Content" => "/content/view/sitemap/2" 
            ), 
            "tag_cloud_url" => array( 
                "DataText" => "Tag cloud", 
                "Content" => "/content/view/tagcloud/2" 
            ), 
            "login_label" => array( 
                "DataText" => "Login" 
            ), 
            "logout_label" => array( 
                "DataText" => "Logout" 
            ), 
            "my_profile_label" => array( 
                "DataText" => "My profile" 
            ), 
            "register_user_label" => array( 
                "DataText" => "Register" 
            ), 
            "rss_feed" => array( 
                "DataText" => "/rss/feed/my_feed" 
            ), 
            "shopping_basket_label" => array( 
                "DataText" => "Shopping basket" 
            ), 
            "site_settings_label" => array( 
                "DataText" => "Site settings" 
            ), 
            "footer_text" => array( 
                "DataText" => "Copyright &#169; " . date( 'Y' ) . " eZ Systems AS. All rights reserved." 
            ), 
            "hide_powered_by" => array( 
                "DataInt" => 0 
            ), 
            "footer_script" => array( 
                "DataText" => "" 
            ) 
        );
        $this->updateContentObjectAttributes( array( 
            'object_id' => $this->setting( 'template_look_object_id' ), 
            'attributes_data' => $templateLookData 
        ) );
    }

    function solutionVersion()
    {
        $version = self::MAJOR_VERSION . '.' . self::MINOR_VERSION;
        return $version;
    }

    function solutionName()
    {
        return 'eZFlow';
    }

    function createTranslationSiteAccesses()
    {
        foreach ($this->setting( 'locales' ) as $locale)
        {
            // Prepare 'SiteLanguageList':
            // make $locale as 'top priority language'
            // and append 'primary language' as fallback language.
            $primaryLanguage = $this->setting( 'primary_language' );
            $languageList = array( 
                $locale 
            );
            if ( $locale != $primaryLanguage )
            {
                $languageList[] = $primaryLanguage;
            }
            $siteaccessTypes = $this->setting( 'siteaccess_urls' );
            // Create siteaccess
            $this->createSiteAccess( array( 
                'src' => array( 
                    'siteaccess' => $this->setting( 'user_siteaccess' ) 
                ), 
                'dst' => array( 
                    'siteaccess' => $this->languageNameFromLocale( $locale ), 
                    'settings' => array( 
                        'site.ini' => array( 
                            'RegionalSettings' => array( 
                                'Locale' => $locale, 
                                'ContentObjectLocale' => $locale, 
                                'TextTranslation' => $locale != 'eng-GB' ? 'enabled' : 'disabled', 
                                'SiteLanguageList' => $languageList 
                            ), 
                            'SiteSettings' => array( 
                                'SiteURL' => $siteaccessTypes['translation'][$this->languageNameFromLocale( $locale )]['url'] 
                            ) 
                        ) 
                    ) 
                ) 
            ) );
        }
    }

    function createiPhoneSiteAccess()
    {
        $this->createSiteAccess( array( 
            'src' => array( 
                'siteaccess' => $this->setting( 'user_siteaccess' ) 
            ), 
            'dst' => array( 
                'siteaccess' => 'iphone', 
                'settings' => array( 
                    'site.ini' => array(
                        'DesignSettings' => array( 
                            'AdditionalSiteDesignList' => array(
                                'ezflow', 'ezwebin', 'base', 'standard'
                                ),
                            'SiteDesign' => 'iphone'
                            ),
                        ) 
                    ) 
                ) 
            ) 
        );
        
    }
    
    function updateiPhoneImageINISettings()
    {
        $settings = $this->siteImageINISettings();
        
        $settings['settings']['AliasSettings'] = array(
            'AliasList' => array_merge(
                $settings['settings']['AliasSettings']['AliasList'],
                array(
                    'iphonelarge', 
                    'ifrontpagegallery', 
                    'iphonethumb', 
                    'iphoneminithumb', 
                    'imainstory1'
                )
            )
        );
        $settings['settings']['iphonelarge'] = array(
            'Reference' => '',
            'Filters' => array(
                'geometry/scalewidthdownonly=285'
            )
        );
        $settings['settings']['iphonethumb'] = array(
            'Reference' => '',
            'Filters' => array(
                'geometry/scalewidthdownonly=139'
            )
        );
        $settings['settings']['iphoneminithumb'] = array(
            'Reference' => '',
            'Filters' => array(
                'geometry/scalewidthdownonly=102.2'
            )
        );
        $settings['settings']['ifrontpagegallery'] = array(
            'Reference' => '',
            'Filters' => array(
                'geometry/scalewidthdownonly=239'
            )
        );
        $settings['settings']['imainstory1'] = array(
            'Reference' => '',
            'Filters' => array(
                'geometry/scalewidthdownonly=302'
            )
        );

        $ini = eZINI::instance( $settings['name'], 'settings/siteaccess/iphone', null, false, null, true );
        $ini->reset();

        $ini->setReadOnlySettingsCheck( false );
        $ini->setVariables( $settings['settings'] );
        $ini->save( $settings['name'], '.append.php', false, null, 'settings/siteaccess/iphone', true );
    }

    ///////////////////////////////////////////////////////////////////////////
    // Setup roles
    ///////////////////////////////////////////////////////////////////////////
    function siteRoles( $params = false )
    {
        $guestAccountsID = $this->setting( 'guest_accounts_id' );
        $anonAccountsID = $this->setting( 'anonymous_accounts_id' );
        $roles = array();
        // Add possibility to read rss by default for anonymous/guests
        $roles[] = array( 
            'name' => 'Anonymous', 
            'policies' => array( 
                array( 
                    'module' => 'rss', 
                    'function' => 'feed' 
                ) 
            ), 
            'assignments' => array( 
                array( 
                    'user_id' => $guestAccountsID 
                ), 
                array( 
                    'user_id' => $anonAccountsID 
                ) 
            ) 
        );
        include_once ('lib/ezutils/classes/ezsys.php');
        // Make sure anonymous can only login to use side
        $roles[] = array( 
            'name' => 'Anonymous', 
            'policies' => array( 
                array( 
                    'module' => 'user', 
                    'function' => 'login', 
                    'limitation' => array( 
                        'SiteAccess' => array( 
                            eZSys::ezcrc32( $this->setting( 'user_siteaccess' ) ) 
                        ) 
                    ) 
                ) 
            ) 
        );
        return $roles;
    }

    ///////////////////////////////////////////////////////////////////////////
    // Setup preferences
    ///////////////////////////////////////////////////////////////////////////
    function sitePreferences()
    {
        $adminAccountID = $this->setting( 'admin_account_id' );
        $preferences = array();
        // Make sure admin starts with:
        // - The 'preview' window set as open by default
        // - The 'content structure' tool is open by default
        // - The 'bookmarks' tool is open by default
        // - The 'roles' and 'policies' windows are open by default
        // - The child list limit is 25 by default
        $preferences[] = array( 
            'user_id' => $adminAccountID, 
            'preferences' => array( 
                array( 
                    'name' => 'admin_navigation_content', 
                    'value' => '1' 
                ), 
                array( 
                    'name' => 'admin_navigation_roles', 
                    'value' => '1' 
                ), 
                array( 
                    'name' => 'admin_navigation_policies', 
                    'value' => '1' 
                ), 
                array( 
                    'name' => 'admin_list_limit', 
                    'value' => '2' 
                ), 
                array( 
                    'name' => 'admin_treemenu', 
                    'value' => '1' 
                ), 
                array( 
                    'name' => 'admin_bookmark_menu', 
                    'value' => '1' 
                ) 
            ) 
        );
        return $preferences;
    }

    ///////////////////////////////////////////////////////////////////////////
    // Post-install siteaccess INI updates
    ///////////////////////////////////////////////////////////////////////////
    function postInstallAdminSiteaccessINIUpdate( $params = false )
    {
        $siteINI = eZINI::instance( 'site.ini.append.php', 'settings/siteaccess/' . $this->setting( 'admin_siteaccess' ), null, false, null, true );
        $siteINI->setVariable( 'DesignSettings', 'SiteDesign', $this->setting( 'admin_siteaccess' ) );
        $siteINI->setVariable( 'DesignSettings', 'AdditionalSiteDesignList', array( 
            'admin', 
            'ezflow' 
        ) );
        $siteINI->setVariable( 'SiteAccessSettings', 'RelatedSiteAccessList', $this->setting( 'all_siteaccess_list' ) );
        $siteINI->save();
    }

    function postInstallUserSiteaccessINIUpdate( $params = false )
    {
        $siteINI = eZINI::instance( "site.ini.append.php", "settings/siteaccess/" . $this->setting( 'user_siteaccess' ), null, false, null, true );
        $siteINI->setVariable( "DesignSettings", "SiteDesign", $this->setting( 'main_site_design' ) );
        $siteINI->setVariable( "DesignSettings", "AdditionalSiteDesignList", array( 
            'ezwebin', 
            'base', 
            'standard' 
        ) );
        $siteINI->setVariable( "SiteAccessSettings", "RelatedSiteAccessList", $this->setting( 'all_siteaccess_list' ) );
        $siteINI->save( false, false, false, false, true, true );
        unset( $siteINI );
    }

    ///////////////////////////////////////////////////////////////////////////
    // Admin siteaccess INI settings
    ///////////////////////////////////////////////////////////////////////////
    function adminINISettings()
    {
        $settings = array();
        $settings[] = $this->adminToolbarINISettings();
        $settings[] = $this->adminContentStructureMenuINISettings();
        $settings[] = $this->adminOverrideINISettings();
        $settings[] = $this->adminSiteINISettings();
        $settings[] = $this->adminContentINISettings();
        $settings[] = $this->adminIconINISettings();
        $settings[] = $this->adminViewCacheINISettings();
        $settings[] = $this->adminODFINISettings();
        $settings[] = $this->adminOEINISettings();
        return $settings;
    }

    function adminContentStructureMenuINISettings()
    {
        $contentStructureMenu = array( 
            'name' => 'contentstructuremenu.ini', 
            'reset_arrays' => true, 
            'settings' => array( 
                'TreeMenu' => array( 
                    'ShowClasses' => array( 
                        'folder', 
                        'user_group', 
                        'documentation_page', 
                        'event_calender', 
                        'frontpage', 
                        'forums' 
                    ) 
                ) 
            ) 
        );
        return $contentStructureMenu;
    }

    function adminToolbarINISettings()
    {
        $toolbar = array( 
            'name' => 'toolbar.ini', 
            'reset_arrays' => true, 
            'settings' => array( 
                'Toolbar' => array( 
                    'AvailableToolBarArray' => array( 
                        0 => 'setup', 
                        1 => 'admin_right', 
                        2 => 'admin_developer' 
                    ) 
                ), 
                'Tool' => array( 
                    'AvailableToolArray' => array( 
                        0 => 'setup_link', 
                        1 => 'admin_current_user', 
                        2 => 'admin_bookmarks', 
                        3 => 'admin_clear_cache', 
                        4 => 'admin_quick_settings' 
                    ) 
                ), 
                'Toolbar_setup' => array( 
                    'Tool' => array( 
                        0 => 'setup_link', 
                        1 => 'setup_link', 
                        2 => 'setup_link', 
                        3 => 'setup_link', 
                        4 => 'setup_link' 
                    ) 
                ), 
                'Toolbar_admin_right' => array( 
                    'Tool' => array( 
                        0 => 'admin_current_user', 
                        1 => 'admin_bookmarks' 
                    ) 
                ), 
                'Toolbar_admin_developer' => array( 
                    'Tool' => array( 
                        0 => 'admin_clear_cache', 
                        1 => 'admin_quick_settings' 
                    ) 
                ), 
                'Tool_setup_link' => array( 
                    'title' => '', 
                    'link_icon' => '', 
                    'url' => '' 
                ), 
                'Tool_setup_link_description' => array( 
                    'title' => 'Title', 
                    'link_icon' => 'Icon', 
                    'url' => 'URL' 
                ), 
                'Tool_setup_setup_link_1' => array( 
                    'title' => 'Classes', 
                    'link_icon' => 'classes.png', 
                    'url' => '/class/grouplist' 
                ), 
                'Tool_setup_setup_link_2' => array( 
                    'title' => 'Cache', 
                    'link_icon' => 'cache.png', 
                    'url' => '/setup/cache' 
                ), 
                'Tool_setup_setup_link_3' => array( 
                    'title' => 'URL translator', 
                    'link_icon' => 'url_translator.png', 
                    'url' => '/content/urltranslator' 
                ), 
                'Tool_setup_setup_link_4' => array( 
                    'title' => 'Settings', 
                    'link_icon' => 'common_ini_settings.png', 
                    'url' => '/content/edit/52' 
                ), 
                'Tool_setup_setup_link_5' => array( 
                    'title' => 'Look and feel', 
                    'link_icon' => 'look_and_feel.png', 
                    'url' => '/content/edit/54' 
                ) 
            ) 
        );
        return $toolbar;
    }

    function adminOverrideINISettings()
    {
        return array( 
            'name' => 'override.ini', 
            'discard_old_values' => true, 
            'settings' => array( 
                'article' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/article.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'article' 
                    ) 
                ), 
                'comment' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/comment.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'comment' 
                    ) 
                ), 
                'company' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/company.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'company' 
                    ) 
                ), 
                'feedback_form' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/feedback_form.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'feedback_form' 
                    ) 
                ), 
                'file' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/file.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'file' 
                    ) 
                ), 
                'flash' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/flash.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'flash' 
                    ) 
                ), 
                'folder' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/folder.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'folder' 
                    ) 
                ), 
                'forum' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/forum.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forum' 
                    ) 
                ), 
                'forum_topic' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/forum_topic.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forum_topic' 
                    ) 
                ), 
                'forum_reply' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/forum_reply.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forum_reply' 
                    ) 
                ), 
                'gallery' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/gallery.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'gallery' 
                    ) 
                ), 
                'image' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'link' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/link.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'link' 
                    ) 
                ), 
                'person' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/person.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'person' 
                    ) 
                ), 
                'poll' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/poll.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'poll' 
                    ) 
                ), 
                'product' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/product.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'product' 
                    ) 
                ), 
                'review' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/review.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'review' 
                    ) 
                ), 
                'quicktime' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/quicktime.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'quicktime' 
                    ) 
                ), 
                'real_video' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/real_video.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'real_video' 
                    ) 
                ), 
                'weblog' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/weblog.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'weblog' 
                    ) 
                ), 
                'windows_media' => array( 
                    'Source' => 'node/view/admin_preview.tpl', 
                    'MatchFile' => 'admin_preview/windows_media.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'windows_media' 
                    ) 
                ), 
                'thumbnail_image' => array( 
                    'Source' => 'node/view/thumbnail.tpl', 
                    'MatchFile' => 'thumbnail/image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'window_controls' => array( 
                    'Source' => 'window_controls.tpl', 
                    'MatchFile' => 'window_controls_user.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'navigation_part_identifier' => 'ezusernavigationpart' 
                    ) 
                ), 
                'windows' => array( 
                    'Source' => 'windows.tpl', 
                    'MatchFile' => 'windows_user.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'navigation_part_identifier' => 'ezusernavigationpart' 
                    ) 
                ), 
                'edit_frontpage' => array( 
                    'Source' => 'content/edit.tpl', 
                    'MatchFile' => 'edit/frontpage.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'frontpage' 
                    ) 
                ), 
                'embed_image' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed_image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'embed-inline_image' => array( 
                    'Source' => 'content/view/embed-inline.tpl', 
                    'MatchFile' => 'embed-inline_image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'embed_node_image' => array( 
                    'Source' => 'node/view/embed.tpl', 
                    'MatchFile' => 'embed_image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'embed-inline_node_image' => array( 
                    'Source' => 'node/view/embed-inline.tpl', 
                    'MatchFile' => 'embed-inline_image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'thumbnail_image_browse' => array( 
                    'Source' => 'node/view/browse_thumbnail.tpl', 
                    'MatchFile' => 'thumbnail/image_browse.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'thumbnail_banner' => array( 
                    'Source' => 'node/view/thumbnail.tpl', 
                    'MatchFile' => 'thumbnail/image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'banner' 
                    ) 
                ), 
                'thumbnail_banner_browse' => array( 
                    'Source' => 'node/view/browse_thumbnail.tpl', 
                    'MatchFile' => 'thumbnail/image_browse.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'banner' 
                    ) 
                ) 
            ) 
        );
    }

    function adminSiteINISettings()
    {
        $settings = array();
        $settings['SiteAccessSettings'] = array( 
            'RequireUserLogin' => 'true' 
        );
        $settings['SiteSettings'] = array( 
            'LoginPage' => 'custom' 
        );
        // Make sure viewcaching works in admin with the new admin interface
        $settings['ContentSettings'] = array( 
            'CachedViewPreferences' => array( 
                'full' => 'admin_navigation_states=0;admin_navigation_content=0;admin_navigation_details=0;admin_navigation_languages=0;admin_navigation_locations=0;admin_navigation_relations=0;admin_navigation_roles=0;admin_navigation_policies=0;admin_navigation_content=0;admin_navigation_translations=0;admin_children_viewmode=list;admin_list_limit=1;admin_edit_show_locations=0;admin_leftmenu_width=10;admin_url_list_limit=10;admin_url_view_limit=10;admin_section_list_limit=1;admin_orderlist_sortfield=user_name;admin_orderlist_sortorder=desc;admin_search_stats_limit=1;admin_treemenu=1;admin_bookmarkmenu=1;admin_left_menu_width=13' 
            ) 
        );
        $settings['SiteAccessSettings'] = array_merge( $settings['SiteAccessSettings'], array( 
            'ShowHiddenNodes' => 'true' 
        ) );
        return array( 
            'name' => 'site.ini', 
            'settings' => $settings 
        );
    }

    function adminContentINISettings()
    {
        $designList = $this->setting( 'design_list' );
        $image = array( 
            'name' => 'content.ini', 
            'reset_arrays' => true, 
            'settings' => array( 
                'VersionView' => array( 
                    'AvailableSiteDesignList' => $designList 
                ) 
            ) 
        );
        return $image;
    }

    function adminIconINISettings()
    {
        $image = array( 
            'name' => 'icon.ini', 
            'reset_arrays' => true, 
            'settings' => array( 
                'IconSettings' => array( 
                    'Theme' => 'crystal-admin', 
                    'Size' => 'normal' 
                ) 
            ) 
        );
        return $image;
    }

    function adminViewCacheINISettings()
    {
        return array( 
            'name' => 'viewcache.ini', 
            'settings' => array( 
                'ViewCacheSettings' => array( 
                    'SmartCacheClear' => 'enabled' 
                ) 
            ) 
        );
    }

    function adminODFINISettings()
    {
        // admin siteaccess uses the same ODF-settings
        return $this->siteODFINISettings();
    }

    function adminOEINISettings()
    {
        return array( 
            'name' => 'ezoe.ini', 
            'settings' => array( 
                'EditorSettings' => array( 
                    'SkinVariant' => 'silver' 
                ) 
            ) 
        );
    }

    ///////////////////////////////////////////////////////////////////////////
    // Common INI settings
    ///////////////////////////////////////////////////////////////////////////
    function commonINISettings()
    {
        $settings = array();
        $settings[] = $this->commonSiteINISettings();
        $settings[] = $this->commonContentINISettings();
        $settings[] = $this->commonMenuINISettings();
        $settings[] = $this->commonViewCacheINISettings();
        $settings[] = $this->commonForumINISettings();
        $settings[] = $this->commonOEAttributesINISettings();
        $settings[] = $this->commonXMLINISettings();
        return $settings;
    }

    function commonSiteINISettings()
    {
        $settings = array();
        $settings['SiteAccessSettings'] = array( 
            'AvailableSiteAccessList' => $this->setting( 'all_siteaccess_list' ) 
        );
        $settings['SiteSettings'] = array( 
            'SiteList' => $this->setting( 'all_siteaccess_list' ), 
            'DefaultAccess' => $this->languageNameFromLocale( $this->setting( 'primary_language' ) ), 
            'RootNodeDepth' => 1 
        );
        $settings['ExtensionSettings'] = array( 
            'ActiveExtensions' => $this->setting( 'extension_list' ) 
        );
        $settings['UserSettings'] = array( 
            'LogoutRedirect' => '/' 
        );
        $settings['EmbedViewModeSettings'] = array( 
            'AvailableViewModes' => array( 
                'embed', 
                'embed-inline' 
            ), 
            'InlineViewModes' => array( 
                'embed-inline' 
            ) 
        );
        $accessType = $this->setting( 'access_type' );
        $siteaccessTypes = $this->setting( 'siteaccess_urls' );
        // set 'language settings'
        $translationSA = array();
        foreach ($siteaccessTypes['translation'] as $name => $urlInfo)
        {
            $translationSA[$name] = ucfirst( $name );
        }
        $settings['RegionalSettings'] = array( 
            'TranslationSA' => $translationSA 
        );
        $portMatch = array();
        $hostMatch = array();
        // get info about translation siteacceses.
        foreach ($siteaccessTypes as $siteaccessList)
        {
            foreach ($siteaccessList as $siteaccessName => $urlInfo)
            {
                switch ($accessType)
                {
                    case 'port':
                        {
                            $port = $urlInfo['port'];
                            $portMatch[$port] = $siteaccessName;
                        }
                        break;
                    case 'hostname':
                        {
                            $host = $urlInfo['host'];
                            $hostMatch[] = $host . ';' . $siteaccessName;
                        }
                }
            }
        }
        switch ($accessType)
        {
            case 'port':
                {
                    $settings['PortAccessSettings'] = $portMatch;
                }
                break;
            case 'hostname':
                {
                    $settings['SiteAccessSettings']['HostMatchMapItems'] = $hostMatch;
                }
                break;
        }
        return array( 
            'name' => 'site.ini', 
            'settings' => $settings 
        );
    }

    function commonMenuINISettings()
    {
        //setup vars
        $settings = array();
        //comment out the line below in order to unlock all menus in ministration interface
        //$settings['TopAdminMenu'] = array( 'Tabs' => array( 'content', 'media', 'shop', 'my_account') );
        return array( 
            'name' => 'menu.ini', 
            'reset_arrays' => true, 
            'settings' => $settings 
        );
    }

    function commonContentINISettings()
    {
        $settings = array( 
            'object' => array( 
                'AvailableClasses' => array( 
                    '0' => 'itemized_sub_items', 
                    '1' => 'itemized_subtree_items', 
                    '2' => 'highlighted_object', 
                    '3' => 'vertically_listed_sub_items', 
                    '4' => 'horizontally_listed_sub_items' 
                ), 
                'ClassDescription' => array( 
                    'itemized_sub_items' => 'Itemized Sub Items', 
                    'itemized_subtree_items' => 'Itemized Subtree Items', 
                    'highlighted_object' => 'Highlighted Object', 
                    'vertically_listed_sub_items' => 'Vertically Listed Sub Items', 
                    'horizontally_listed_sub_items' => 'Horizontally Listed Sub Items' 
                ), 
                'CustomAttributes' => array( 
                    '0' => 'offset', 
                    '1' => 'limit' 
                ), 
                'CustomAttributesDefaults' => array( 
                    'offset' => '0', 
                    'limit' => '5' 
                ) 
            ), 
            'embed' => array( 
                'AvailableClasses' => array( 
                    '0' => 'itemized_sub_items', 
                    '1' => 'itemized_subtree_items', 
                    '2' => 'highlighted_object', 
                    '3' => 'vertically_listed_sub_items', 
                    '4' => 'horizontally_listed_sub_items' 
                ), 
                'ClassDescription' => array( 
                    'itemized_sub_items' => 'Itemized Sub Items', 
                    'itemized_subtree_items' => 'Itemized Subtree Items', 
                    'highlighted_object' => 'Highlighted Object', 
                    'vertically_listed_sub_items' => 'Vertically Listed Sub Items', 
                    'horizontally_listed_sub_items' => 'Horizontally Listed Sub Items' 
                ), 
                'CustomAttributes' => array( 
                    '0' => 'offset', 
                    '1' => 'limit' 
                ), 
                'CustomAttributesDefaults' => array( 
                    'offset' => '0', 
                    'limit' => '5' 
                ) 
            ), 
            'table' => array( 
                'AvailableClasses' => array( 
                    '0' => 'list', 
                    '1' => 'cols', 
                    '2' => 'comparison', 
                    '3' => 'default' 
                ), 
                'ClassDescription' => array( 
                    'list' => 'List', 
                    'cols' => 'Timetable', 
                    'comparison' => 'Comparison Table', 
                    'default' => 'Default' 
                ), 
                'CustomAttributes' => array( 
                    '0' => 'summary', 
                    '1' => 'caption' 
                ), 
                'Defaults' => array( 
                    'rows' => '2', 
                    'cols' => '2', 
                    'width' => '100%', 
                    'border' => '0', 
                    'class' => 'default' 
                ) 
            ), 
            'td' => array( 
                'CustomAttributes' => array( 
                    '0' => 'valign' 
                ) 
            ), 
            'th' => array( 
                'CustomAttributes' => array( 
                    '0' => 'scope', 
                    '1' => 'abbr', 
                    '2' => 'valign' 
                ) 
            ), 
            'factbox' => array( 
                'CustomAttributes' => array( 
                    '0' => 'align', 
                    '1' => 'title' 
                ), 
                'CustomAttributesDefaults' => array( 
                    'align' => 'right', 
                    'title' => 'factbox' 
                ) 
            ), 
            'quote' => array( 
                'CustomAttributes' => array( 
                    '0' => 'align', 
                    '1' => 'author' 
                ), 
                'CustomAttributesDefaults' => array( 
                    'align' => 'right', 
                    'autor' => 'Quote author' 
                ) 
            ), 
            'CustomTagSettings' => array( 
                'AvailableCustomTags' => array( 
                    '0' => 'underline' 
                ), 
                'IsInline' => array( 
                    'underline' => 'true' 
                ) 
            ), 
            'embed-type_images' => array( 
                'AvailableClasses' => array() 
            ) 
        );
        return array( 
            'name' => 'content.ini', 
            'settings' => $settings 
        );
    }

    function commonViewCacheINISettings()
    {
        $settings = array( 
            'ViewCacheSettings' => array( 
                'SmartCacheClear' => 'enabled', 
                'ClearRelationTypes' => array( 
                    'common', 
                    'reverse_common', 
                    'reverse_embedded', 
                    'reverse_attribute' 
                ) 
            ), 
            'forum_reply' => array( 
                'DependentClassIdentifier' => array( 
                    'forum_topic', 
                    'forum' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating', 
                    '3' => 'siblings' 
                ) 
            ), 
            'forum_topic' => array( 
                'DependentClassIdentifier' => array( 
                    'forum' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating', 
                    '3' => 'siblings' 
                ) 
            ), 
            'folder' => array( 
                'DependentClassIdentifier' => array( 
                    '0' => 'folder' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating' 
                ) 
            ), 
            'gallery' => array( 
                'DependentClassIdentifier' => array( 
                    '0' => 'folder' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating' 
                ) 
            ), 
            'image' => array( 
                'DependentClassIdentifier' => array( 
                    '0' => 'gallery' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating', 
                    '3' => 'siblings' 
                ) 
            ), 
            'event' => array( 
                'DependentClassIdentifier' => array( 
                    '0' => 'event_calender' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating' 
                ) 
            ), 
            'article' => array( 
                'DependentClassIdentifier' => array( 
                    '0' => 'folder', 
                    '1' => 'frontpage' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating' 
                ) 
            ), 
            'article_mainpage' => array( 
                'DependentClassIdentifier' => array( 
                    '0' => 'folder', 
                    '1' => 'frontpage' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating' 
                ) 
            ), 
            'article_subpage' => array( 
                'DependentClassIdentifier' => array( 
                    '0' => 'article_mainpage' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating', 
                    '3' => 'siblings' 
                ) 
            ), 
            'blog_post' => array( 
                'DependentClassIdentifier' => array( 
                    '0' => 'frontpage', 
                    '1' => 'blog' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating' 
                ) 
            ), 
            'product' => array( 
                'DependentClassIdentifier' => array( 
                    '0' => 'folder', 
                    '1' => 'frontpage' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating' 
                ) 
            ), 
            'infobox' => array( 
                'DependentClassIdentifier' => array( 
                    '0' => 'folder' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating' 
                ) 
            ), 
            'documentation_page' => array( 
                'DependentClassIdentifier' => array( 
                    '0' => 'documentation_page' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating' 
                ) 
            ), 
            'banner' => array( 
                'DependentClassIdentifier' => array( 
                    '0' => 'frontpage' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating' 
                ) 
            ), 
            'geo_article' => array( 
                'DependentClassIdentifier' => array( 
                    '0' => 'frontpage' 
                ), 
                'ClearCacheMethod' => array( 
                    '0' => 'object', 
                    '1' => 'parent', 
                    '2' => 'relating' 
                ) 
            )
        );
        return array( 
            'name' => 'viewcache.ini', 
            'settings' => $settings 
        );
    }

    function commonForumINISettings()
    {
        $settings = array();
        $settings['ForumSettings'] = array( 
            'StickyUserGroupArray' => array( 
                12 
            ) 
        );
        return array( 
            'name' => 'forum.ini', 
            'reset_arrays' => false, 
            'settings' => $settings 
        );
    }

    function commonOEAttributesINISettings()
    {
        $settings = array( 
            'CustomAttribute_table_summary' => array( 
                'Name' => 'Summary (WAI)', 
                'Required' => 'true' 
            ), 
            'CustomAttribute_scope' => array( 
                'Name' => 'Scope', 
                'Title' => 'The scope attribute defines a way to associate header cells and data cells in a table.', 
                'Type' => 'select', 
                'Selection' => array( 
                    '0' => '', 
                    'col' => 'Column', 
                    'row' => 'Row' 
                ) 
            ), 
            'CustomAttribute_valign' => array( 
                'Title' => 'Lets you define the vertical alignment of the table cell/ header.', 
                'Type' => 'select', 
                'Selection' => array( 
                    '0' => '', 
                    'top' => 'Top', 
                    'middle' => 'Middle', 
                    'bottom' => 'Bottom', 
                    'baseline' => 'Baseline' 
                ) 
            ), 
            'Attribute_table_border' => array( 
                'Type' => 'htmlsize', 
                'AllowEmpty' => 'true' 
            ), 
            'CustomAttribute_embed_offset' => array( 
                'Type' => 'int', 
                'AllowEmpty' => 'true' 
            ), 
            'CustomAttribute_embed_limit' => array( 
                'Type' => 'int', 
                'AllowEmpty' => 'true' 
            ) 
        );
        return array( 
            'name' => 'ezoe_attributes.ini', 
            'settings' => $settings 
        );
    }

    function commonXMLINISettings()
    {
        return array( 
            'name' => 'ezxml.ini', 
            'settings' => array( 
                'TagSettings' => array( 
                    'TagPresets' => array( 
                        '0' => '', 
                        'mini' => 'Simple formatting' 
                    ) 
                ) 
            ) 
        );
    }

    ///////////////////////////////////////////////////////////////////////////
    // User siteaccess INI settings
    ///////////////////////////////////////////////////////////////////////////
    function siteINISettings()
    {
        $settings = array();
        $settings[] = $this->siteMenuINISettings();
        $settings[] = $this->siteOverrideINISettings();
        $settings[] = $this->siteToolbarINISettings();
        $settings[] = $this->siteSiteINISettings();
        $settings[] = $this->siteImageINISettings();
        $settings[] = $this->siteContentINISettings();
        $settings[] = $this->siteDesignINISettings();
        $settings[] = $this->siteBrowseINISettings();
        $settings[] = $this->siteTemplateINISettings();
        $settings[] = $this->siteContentStructureMenuINISettings();
        $settings[] = $this->siteODFINISettings();
        $settings[] = $this->siteOEINISettings();
        return $settings;
    }

    function siteSiteINISettings()
    {
        $settings = array();
        $settings['RegionalSettings'] = array( 
            'ShowUntranslatedObjects' => 'disabled' 
        );
        $settings['SiteAccessSettings'] = array( 
            'RequireUserLogin' => 'false', 
            'ShowHiddenNodes' => 'false' 
        );
        $siteaccessUrl = $this->setting( 'siteaccess_urls' );
        $adminSiteaccessName = $this->setting( 'admin_siteaccess' );
        $settings['SiteSettings'] = array( 
            'LoginPage' => 'embedded', 
            'AdditionalLoginFormActionURL' => 'http://' . $siteaccessUrl['admin'][$adminSiteaccessName]['url'] . '/user/login' 
        );
        $settings['Session'] = array( 
            'SessionNamePerSiteAccess' => 'disabled' 
        );
        return array( 
            'name' => 'site.ini', 
            'settings' => $settings 
        );
    }

    function siteDesignINISettings()
    {
        $settings = array( 
            'name' => 'design.ini', 
            'reset_arrays' => false, 
            'settings' => array( 
                'JavaScriptSettings' => array( 
                    'JavaScriptList' => array( 
                        'insertmedia.js'
                    ) 
                ), 
                'StylesheetSettings' => array( 
                    'CSSFileList' => array( 
                    ) 
                ) 
            ) 
        );
        return $settings;
    }

    function siteContentStructureMenuINISettings()
    {
        $contentStructureMenu = array( 
            'name' => 'contentstructuremenu.ini', 
            'reset_arrays' => true, 
            'settings' => array( 
                'TreeMenu' => array( 
                    'ShowClasses' => array( 
                        'folder', 
                        'documentation_page', 
                        'frontpage', 
                        'forums' 
                    ), 
                    'ToolTips' => 'disabled' 
                ) 
            ) 
        );
        return $contentStructureMenu;
    }

    function siteMenuINISettings()
    {
        return array( 
            'name' => 'menu.ini', 
            'reset_arrays' => true, 
            'settings' => array( 
                'MenuSettings' => array( 
                    'AvailableMenuArray' => array( 
                        'TopOnly', 
                        'LeftOnly', 
                        'DoubleTop', 
                        'LeftTop' 
                    ) 
                ), 
                'SelectedMenu' => array( 
                    'CurrentMenu' => 'DoubleTop', 
                    'TopMenu' => 'double_top', 
                    'LeftMenu' => '' 
                ), 
                'TopOnly' => array( 
                    'TitleText' => 'Only top menu', 
                    'MenuThumbnail' => 'menu/top_only.jpg', 
                    'TopMenu' => 'flat_top', 
                    'LeftMenu' => '' 
                ), 
                'LeftOnly' => array( 
                    'TitleText' => 'Left menu', 
                    'MenuThumbnail' => 'menu/left_only.jpg', 
                    'TopMenu' => '', 
                    'LeftMenu' => 'flat_left' 
                ), 
                'DoubleTop' => array( 
                    'TitleText' => 'Double top menu', 
                    'MenuThumbnail' => 'menu/double_top.jpg', 
                    'TopMenu' => 'double_top', 
                    'LeftMenu' => '' 
                ), 
                'LeftTop' => array( 
                    'TitleText' => 'Left and top', 
                    'MenuThumbnail' => 'menu/left_top.jpg', 
                    'TopMenu' => 'flat_top', 
                    'LeftMenu' => 'flat_left' 
                ), 
                'MenuContentSettings' => array( 
                    'TopIdentifierList' => array( 
                        'folder', 
                        'feedback_form' 
                    ), 
                    'LeftIdentifierList' => array( 
                        'folder', 
                        'feedback_form' 
                    ) 
                ) 
            ) 
        );
    }

    function siteOverrideINISettings()
    {
        return array( 
            'name' => 'override.ini', 
            'discard_old_values' => true, 
            'settings' => array( 
                'block_2_items1' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/2_items1.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Manual2Items', 
                        'view' => '2_items1' 
                    ) 
                ), 
                'block_2_items2' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/2_items2.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Manual2Items', 
                        'view' => '2_items2' 
                    ) 
                ), 
                'block_main_story1' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/main_story1.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'MainStory', 
                        'view' => 'main_story1' 
                    ) 
                ), 
                'block_main_story2' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/main_story2.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'MainStory', 
                        'view' => 'main_story2' 
                    ) 
                ), 
                'block_main_story3' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/main_story3.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'MainStory', 
                        'view' => 'main_story3' 
                    ) 
                ), 
                'block_3_items1' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/3_items1.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Manual3Items', 
                        'view' => '3_items1' 
                    ) 
                ), 
                'block_3_items2' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/3_items2.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Manual3Items', 
                        'view' => '3_items2' 
                    ) 
                ), 
                'block_3_items3' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/3_items3.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Manual3Items', 
                        'view' => '3_items3' 
                    ) 
                ), 
                'block_4_items1' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/4_items1.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Manual4Items', 
                        'view' => '4_items1' 
                    ) 
                ), 
                'block_4_items2' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/4_items2.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Manual4Items', 
                        'view' => '4_items2' 
                    ) 
                ), 
                'block_4_items3' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/4_items3.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Manual4Items', 
                        'view' => '4_items3' 
                    ) 
                ), 
                'block_5_items1' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/5_items1.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Manual5Items', 
                        'view' => '5_items1' 
                    ) 
                ), 
                'dynamic_3_items1' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/dynamic_3_items1.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Dynamic3Items', 
                        'view' => '3_items1' 
                    ) 
                ), 
                'block_gallery1' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/gallery1.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Gallery', 
                        'view' => 'gallery1' 
                    ) 
                ), 
                'block_gmap' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/gmap.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'GMap', 
                        'view' => 'gmap' 
                    ) 
                ), 
                'block_online_users' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/online_users.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'OnlineUsers', 
                        'view' => 'onlineusers' 
                    ) 
                ), 
                'block_keyword' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/keyword.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Keyword', 
                        'view' => 'keyword' 
                    ) 
                ), 
                'block_multimediacarousel' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/multimediacarousel.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'MultimediaCarousel', 
                        'view' => 'multimediacarousel' 
                    ) 
                ), 
                'block_search' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/search.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Search', 
                        'view' => 'search' 
                    ) 
                ), 
                'block_ads' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/ads.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Ads', 
                        'view' => 'ads' 
                    ) 
                ), 
                'block_latest_content' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/latest_content.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'LatestContent', 
                        'view' => 'latest_content' 
                    ) 
                ), 
                'block_top_rated' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/top_rated.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'TopRated', 
                        'view' => 'toprated' 
                    ) 
                ), 
                'block_gmapitems_geo_located_content' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/geo_located_content.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'GMapItems', 
                        'view' => 'geo_located_content' 
                    ) 
                ), 
                'block_feed_reader' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/feed_reader.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'FeedReader', 
                        'view' => 'feed_reader' 
                    ) 
                ), 
                'block_video' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/video.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Video', 
                        'view' => 'video' 
                    ) 
                ), 
                'block_itemlist1' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/itemlist1.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'ItemList', 
                        'view' => 'itemlist1' 
                    ) 
                ), 
                'block_itemlist1' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/itemlist1.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'ItemList', 
                        'view' => 'itemlist1' 
                    ) 
                ), 
                'block_itemlist2' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/itemlist2.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'ItemList', 
                        'view' => 'itemlist2' 
                    ) 
                ), 
                'block_itemlist3' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/itemlist3.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'ItemList', 
                        'view' => 'itemlist3' 
                    ) 
                ), 
                'block_banner1' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/banner1.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Banner', 
                        'view' => 'banner1' 
                    ) 
                ), 
                'block_banner2' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/banner2.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Banner', 
                        'view' => 'banner2' 
                    ) 
                ), 
                'block_tag_cloud' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/tag_cloud.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'TagCloud', 
                        'view' => 'tag_cloud' 
                    ) 
                ), 
                'block_poll' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/poll.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'Poll', 
                        'view' => 'poll' 
                    ) 
                ), 
                'block_flash_recorder' => array( 
                    'Source' => 'block/view/view.tpl', 
                    'MatchFile' => 'block/flash_recorder.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'type' => 'FlashRecorder', 
                        'view' => 'flash_recorder' 
                    ) 
                ), 
                'block_item_article' => array( 
                    'Source' => 'node/view/block_item.tpl', 
                    'MatchFile' => 'block_item/article.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'article' 
                    ) 
                ), 
                'block_item_comment' => array( 
                    'Source' => 'node/view/block_item.tpl', 
                    'MatchFile' => 'block_item/comment.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'comment' 
                    ) 
                ), 
                'block_item_image' => array( 
                    'Source' => 'node/view/block_item.tpl', 
                    'MatchFile' => 'block_item/image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'full_article' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/article.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'article' 
                    ) 
                ), 
                'full_geo_article' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/geo_article.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'geo_article' 
                    ) 
                ), 
                'full_article_mainpage' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/article_mainpage.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'article_mainpage' 
                    ) 
                ), 
                'full_article_subpage' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/article_subpage.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'article_subpage' 
                    ) 
                ), 
                'full_banner' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/banner.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'banner' 
                    ) 
                ), 
                'full_blog' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/blog.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'blog' 
                    ) 
                ), 
                'full_blog_post' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/blog_post.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'blog_post' 
                    ) 
                ), 
                'full_comment' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/comment.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'comment' 
                    ) 
                ), 
                'full_documentation_page' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/documentation_page.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'documentation_page' 
                    ) 
                ), 
                'full_event_calendar' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/event_calendar.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'event_calendar' 
                    ) 
                ), 
                'full_event' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/event.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'event' 
                    ) 
                ), 
                'full_feedback_form' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/feedback_form.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'feedback_form' 
                    ) 
                ), 
                'full_file' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/file.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'file' 
                    ) 
                ), 
                'full_flash' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/flash.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'flash' 
                    ) 
                ), 
                'full_flash_player' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/flash_player.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'flash_player' 
                    ) 
                ), 
                'full_flash_recorder' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/flash_recorder.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'flash_recorder' 
                    ) 
                ), 
                'full_folder' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/folder.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'folder' 
                    ) 
                ), 
                'full_forum' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/forum.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forum' 
                    ) 
                ), 
                'full_forum_reply' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/forum_reply.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forum_reply' 
                    ) 
                ), 
                'full_forum_topic' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/forum_topic.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forum_topic' 
                    ) 
                ), 
                'full_forums' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/forums.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forums' 
                    ) 
                ), 
                'full_frontpage' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/frontpage.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'frontpage' 
                    ) 
                ), 
                'full_gallery' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/gallery.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'gallery' 
                    ) 
                ), 
                'full_image' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'full_infobox' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/infobox.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'infobox' 
                    ) 
                ), 
                'full_link' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/link.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'link' 
                    ) 
                ), 
                'full_multicalendar' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/multicalendar.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'multicalendar' 
                    ) 
                ), 
                'full_poll' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/poll.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'poll' 
                    ) 
                ), 
                'full_product' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/product.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'product' 
                    ) 
                ), 
                'full_quicktime' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/quicktime.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'quicktime' 
                    ) 
                ), 
                'full_real_video' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/real_video.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'real_video' 
                    ) 
                ), 
                'full_silverlight' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/silverlight.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'silverlight' 
                    ) 
                ), 
                'full_windows_media' => array( 
                    'Source' => 'node/view/full.tpl', 
                    'MatchFile' => 'full/windows_media.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'windows_media' 
                    ) 
                ), 
                'line_article' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/article.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'article' 
                    ) 
                ), 
                'line_geo_article' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/geo_article.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'geo_article' 
                    ) 
                ), 
                'line_article_mainpage' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/article_mainpage.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'article_mainpage' 
                    ) 
                ), 
                'line_article_subpage' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/article_subpage.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'article_subpage' 
                    ) 
                ), 
                'line_banner' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/banner.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'banner' 
                    ) 
                ), 
                'line_blog' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/blog.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'blog' 
                    ) 
                ), 
                'line_blog_post' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/blog_post.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'blog_post' 
                    ) 
                ), 
                'line_comment' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/comment.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'comment' 
                    ) 
                ), 
                'line_documentation_page' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/documentation_page.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'documentation_page' 
                    ) 
                ), 
                'line_event_calendar' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/event_calendar.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'event_calendar' 
                    ) 
                ), 
                'line_event' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/event.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'event' 
                    ) 
                ), 
                'line_feedback_form' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/feedback_form.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'feedback_form' 
                    ) 
                ), 
                'line_file' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/file.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'file' 
                    ) 
                ), 
                'line_flash' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/flash.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'flash' 
                    ) 
                ), 
                'line_folder' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/folder.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'folder' 
                    ) 
                ), 
                'line_forum' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/forum.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forum' 
                    ) 
                ), 
                'line_forum_reply' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/forum_reply.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forum_reply' 
                    ) 
                ), 
                'line_forum_topic' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/forum_topic.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forum_topic' 
                    ) 
                ), 
                'line_forums' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/forums.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forums' 
                    ) 
                ), 
                'line_gallery' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/gallery.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'gallery' 
                    ) 
                ), 
                'line_image' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'line_infobox' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/infobox.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'infobox' 
                    ) 
                ), 
                'line_link' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/link.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'link' 
                    ) 
                ), 
                'line_multicalendar' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/multicalendar.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'multicalendar' 
                    ) 
                ), 
                'line_poll' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/poll.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'poll' 
                    ) 
                ), 
                'line_product' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/product.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'product' 
                    ) 
                ), 
                'line_silverlight' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/silverlight.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'silverlight' 
                    ) 
                ), 
                'line_quicktime' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/quicktime.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'quicktime' 
                    ) 
                ), 
                'line_real_video' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/real_video.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'real_video' 
                    ) 
                ), 
                'line_windows_media' => array( 
                    'Source' => 'node/view/line.tpl', 
                    'MatchFile' => 'line/windows_media.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'windows_media' 
                    ) 
                ), 
                'edit_comment' => array( 
                    'Source' => 'content/edit.tpl', 
                    'MatchFile' => 'edit/comment.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'comment' 
                    ) 
                ), 
                'edit_file' => array( 
                    'Source' => 'content/edit.tpl', 
                    'MatchFile' => 'edit/file.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'file' 
                    ) 
                ), 
                'edit_forum_topic' => array( 
                    'Source' => 'content/edit.tpl', 
                    'MatchFile' => 'edit/forum_topic.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forum_topic' 
                    ) 
                ), 
                'edit_ezsubtreesubscription_forum_topic' => array( 
                    'Source' => 'content/datatype/edit/ezsubtreesubscription.tpl', 
                    'MatchFile' => 'datatype/edit/ezsubtreesubscription/forum_topic.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forum_topic' 
                    ) 
                ), 
                'edit_forum_reply' => array( 
                    'Source' => 'content/edit.tpl', 
                    'MatchFile' => 'edit/forum_reply.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forum_reply' 
                    ) 
                ), 
                'edit_frontpage' => array( 
                    'Source' => 'content/edit.tpl', 
                    'MatchFile' => 'edit/frontpage.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'frontpage' 
                    ) 
                ), 
                'highlighted_object' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/highlighted_object.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'classification' => 'highlighted_object' 
                    ) 
                ), 
                'embed_article' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/article.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'article' 
                    ) 
                ), 
                'embed_banner' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/banner.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'banner' 
                    ) 
                ), 
                'embed_file' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/file.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'file' 
                    ) 
                ), 
                'embed_flash' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/flash.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'flash' 
                    ) 
                ), 
                'embed_flash_player' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/flash_player.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'flash_player' 
                    ) 
                ), 
                'itemized_sub_items' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/itemized_sub_items.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'classification' => 'itemized_sub_items' 
                    ) 
                ), 
                'vertically_listed_sub_items' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/vertically_listed_sub_items.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'classification' => 'vertically_listed_sub_items' 
                    ) 
                ), 
                'horizontally_listed_sub_items' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/horizontally_listed_sub_items.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'classification' => 'horizontally_listed_sub_items' 
                    ) 
                ), 
                'itemized_subtree_items' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/itemized_subtree_items.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'classification' => 'itemized_subtree_items' 
                    ) 
                ), 
                'embed_folder' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/folder.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'folder' 
                    ) 
                ), 
                'embed_forum' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/forum.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forum' 
                    ) 
                ), 
                'embed_gallery' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/gallery.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'gallery' 
                    ) 
                ), 
                'embed_image' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'embed_poll' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/poll.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'poll' 
                    ) 
                ), 
                'embed_product' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/product.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'product' 
                    ) 
                ), 
                'embed_quicktime' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/quicktime.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'quicktime' 
                    ) 
                ), 
                'embed_real_video' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/real_video.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'real_video' 
                    ) 
                ), 
                'embed_windows_media' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/windows_media.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'windows_media' 
                    ) 
                ), 
                'embed_inline_image' => array( 
                    'Source' => 'content/view/embed-inline.tpl', 
                    'MatchFile' => 'embed-inline/image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'embed_itemizedsubitems_gallery' => array( 
                    'Source' => 'content/view/itemizedsubitems.tpl', 
                    'MatchFile' => 'itemizedsubitems/gallery.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'gallery' 
                    ) 
                ), 
                'embed_itemizedsubitems_forum' => array( 
                    'Source' => 'content/view/itemizedsubitems.tpl', 
                    'MatchFile' => 'itemizedsubitems/forum.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'forum' 
                    ) 
                ), 
                'embed_itemizedsubitems_folder' => array( 
                    'Source' => 'content/view/itemizedsubitems.tpl', 
                    'MatchFile' => 'itemizedsubitems/folder.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'folder' 
                    ) 
                ), 
                'embed_itemizedsubitems_event_calendar' => array( 
                    'Source' => 'content/view/itemizedsubitems.tpl', 
                    'MatchFile' => 'itemizedsubitems/event_calendar.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'event_calendar' 
                    ) 
                ), 
                'embed_itemizedsubitems_documentation_page' => array( 
                    'Source' => 'content/view/itemizedsubitems.tpl', 
                    'MatchFile' => 'itemizedsubitems/documentation_page.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'documentation_page' 
                    ) 
                ), 
                'embed_itemizedsubitems_itemized_sub_items' => array( 
                    'Source' => 'content/view/itemizedsubitems.tpl', 
                    'MatchFile' => 'itemizedsubitems/itemized_sub_items.tpl', 
                    'Subdir' => 'templates' 
                ), 
                'embed_event_calendar' => array( 
                    'Source' => 'content/view/embed.tpl', 
                    'MatchFile' => 'embed/event_calendar.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'event_calendar' 
                    ) 
                ), 
                'embed_horizontallylistedsubitems_article' => array( 
                    'Source' => 'node/view/horizontallylistedsubitems.tpl', 
                    'MatchFile' => 'horizontallylistedsubitems/article.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'article' 
                    ) 
                ), 
                'embed_horizontallylistedsubitems_event' => array( 
                    'Source' => 'node/view/horizontallylistedsubitems.tpl', 
                    'MatchFile' => 'horizontallylistedsubitems/event.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'event' 
                    ) 
                ), 
                'embed_horizontallylistedsubitems_image' => array( 
                    'Source' => 'node/view/horizontallylistedsubitems.tpl', 
                    'MatchFile' => 'horizontallylistedsubitems/image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'embed_horizontallylistedsubitems_product' => array( 
                    'Source' => 'node/view/horizontallylistedsubitems.tpl', 
                    'MatchFile' => 'horizontallylistedsubitems/product.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'product' 
                    ) 
                ), 
                'factbox' => array( 
                    'Source' => 'content/datatype/view/ezxmltags/factbox.tpl', 
                    'MatchFile' => 'datatype/ezxmltext/factbox.tpl', 
                    'Subdir' => 'templates' 
                ), 
                'quote' => array( 
                    'Source' => 'content/datatype/view/ezxmltags/quote.tpl', 
                    'MatchFile' => 'datatype/ezxmltext/quote.tpl', 
                    'Subdir' => 'templates' 
                ), 
                'table_cols' => array( 
                    'Source' => 'content/datatype/view/ezxmltags/table.tpl', 
                    'MatchFile' => 'datatype/ezxmltext/table_cols.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'classification' => 'cols' 
                    ) 
                ), 
                'table_comparison' => array( 
                    'Source' => 'content/datatype/view/ezxmltags/table.tpl', 
                    'MatchFile' => 'datatype/ezxmltext/table_comparison.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'classification' => 'comparison' 
                    ) 
                ), 
                'image_galleryline' => array( 
                    'Source' => 'node/view/galleryline.tpl', 
                    'MatchFile' => 'galleryline/image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'flash_player_galleryline' => array( 
                    'Source' => 'node/view/galleryline.tpl', 
                    'MatchFile' => 'galleryline/flash_player.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'flash_player' 
                    ) 
                ), 
                'image_galleryslide' => array( 
                    'Source' => 'node/view/galleryslide.tpl', 
                    'MatchFile' => 'galleryslide/image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'article_listitem' => array( 
                    'Source' => 'node/view/listitem.tpl', 
                    'MatchFile' => 'listitem/article.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'article' 
                    ) 
                ), 
                'image_listitem' => array( 
                    'Source' => 'node/view/listitem.tpl', 
                    'MatchFile' => 'listitem/image.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'image' 
                    ) 
                ), 
                'billboard_banner' => array( 
                    'Source' => 'content/view/billboard.tpl', 
                    'MatchFile' => 'billboard/banner.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'banner' 
                    ) 
                ), 
                'billboard_flash' => array( 
                    'Source' => 'content/view/billboard.tpl', 
                    'MatchFile' => 'billboard/flash.tpl', 
                    'Subdir' => 'templates', 
                    'Match' => array( 
                        'class_identifier' => 'flash' 
                    ) 
                ) 
            ) 
        );
    }

    function siteToolbarINISettings()
    {
        $toolbar = array( 
            'name' => 'toolbar.ini', 
            'reset_arrays' => true, 
            'settings' => array( 
                'Toolbar_right' => array( 
                    'Tool' => array( 
                        'node_list' 
                    ) 
                ), 
                'Toolbar_top' => array( 
                    'Tool' => array( 
                        'login', 
                        'searchbox' 
                    ) 
                ), 
                'Toolbar_bottom' => array( 
                    'Tool' => array() 
                ), 
                'Tool_right_node_list_1' => array( 
                    'parent_node' => '2', 
                    'title' => 'Latest', 
                    'show_subtree' => '', 
                    'limit' => 5 
                ) 
            ) 
        );
        return $toolbar;
    }

    function siteImageINISettings()
    {
        $settings = array( 
            'name' => 'image.ini', 
            'reset_arrays' => true, 
            'settings' => array( 
                'AliasSettings' => array( 
                    'AliasList' => array( 
                        '0' => 'small', 
                        '1' => 'medium', 
                        '2' => 'listitem', 
                        '3' => 'articleimage', 
                        '4' => 'articlethumbnail', 
                        '5' => 'gallerythumbnail', 
                        '6' => 'galleryline', 
                        '7' => 'imagelarge', 
                        '8' => 'large', 
                        '9' => 'rss', 
                        '10' => 'logo', 
                        '11' => 'infoboximage', 
                        '12' => 'billboard' 
                    ) 
                ), 
                'small' => array( 
                    'Reference' => '', 
                    'Filters' => array( 
                        '0' => 'geometry/scaledownonly=100;160' 
                    ) 
                ), 
                'medium' => array( 
                    'Reference' => '', 
                    'Filters' => array( 
                        '0' => 'geometry/scaledownonly=200;290' 
                    ) 
                ), 
                'large' => array( 
                    'Reference' => '', 
                    'Filters' => array( 
                        '0' => 'geometry/scaledownonly=360;440' 
                    ) 
                ), 
                'rss' => array( 
                    'Reference' => '', 
                    'Filters' => array( 
                        '0' => 'geometry/scale=88;31' 
                    ) 
                ), 
                'logo' => array( 
                    'Reference' => '', 
                    'Filters' => array( 
                        '0' => 'geometry/scaleheight=36' 
                    ) 
                ), 
                'listitem' => array( 
                    'Reference' => '', 
                    'Filters' => array( 
                        '0' => 'geometry/scaledownonly=130;190' 
                    ) 
                ), 
                'articleimage' => array( 
                    'Reference' => '', 
                    'Filters' => array( 
                        '0' => 'geometry/scaledownonly=170;350' 
                    ) 
                ), 
                'articlethumbnail' => array( 
                    'Reference' => '', 
                    'Filters' => array( 
                        '0' => 'geometry/scaledownonly=70;150' 
                    ) 
                ), 
                'gallerythumbnail' => array( 
                    'Reference' => '', 
                    'Filters' => array( 
                        '0' => 'geometry/scaledownonly=105;100' 
                    ) 
                ), 
                'galleryline' => array( 
                    'Reference' => '', 
                    'Filters' => array( 
                        '0' => 'geometry/scaledownonly=70;150' 
                    ) 
                ), 
                'imagelarge' => array( 
                    'Reference' => '', 
                    'Filters' => array( 
                        '0' => 'geometry/scaledownonly=550;730' 
                    ) 
                ), 
                'infoboximage' => array( 
                    'Reference' => '', 
                    'Filters' => array( 
                        '0' => 'geometry/scalewidth=75' 
                    ) 
                ), 
                'billboard' => array( 
                    'Reference' => '', 
                    'Filters' => array( 
                        '0' => 'geometry/scalewidth=764' 
                    ) 
                ) 
            ) 
        );
        return $settings;
    }

    function siteContentINISettings()
    {
        $settings = array( 
            'name' => 'content.ini', 
            'reset_arrays' => false, 
            'settings' => array( 
                'VersionView' => array( 
                    'AvailableSiteDesignList' => array( 
                        $this->setting( 'main_site_design' ) 
                    ) 
                ), 
                'ObjectRelationDataTypeSettings' => array( 
                    'ClassAttributeStartNode' => array( 
                        '236;AddRelatedBannerImageToDataType' 
                    ) 
                ) 
            ) 
        );
        return $settings;
    }

    function siteBrowseINISettings()
    {
        $settings = array( 
            'name' => 'browse.ini', 
            'reset_arrays' => false, 
            'settings' => array( 
                'BrowseSettings' => array( 
                    'AliasList' => array( 
                        'banners' => '59' 
                    ) 
                ), 
                'AddRelatedBannerImageToDataType' => array( 
                    'StartNode' => 'banners', 
                    'SelectionType' => 'single', 
                    'ReturnType' => 'ObjectID' 
                ) 
            ) 
        );
        return $settings;
    }

    function siteTemplateINISettings()
    {
        $settings = array( 
            'name' => 'template.ini', 
            'settings' => array( 
                'CharsetSettings' => array( 
                    'DefaultTemplateCharset' => 'utf-8' 
                ) 
            ) 
        );
        return $settings;
    }

    function siteODFINISettings()
    {
        // update 'article' class attributes info
        $articleExtraAttributes = array( 
            'caption' => 'caption', 
            'publish_date' => 'publish_date', 
            'unpublish_date' => 'unpublish_date' 
        );
        return array( 
            'name' => 'odf.ini', 
            'settings' => array( 
                'article' => array( 
                    'Attribute' => $articleExtraAttributes 
                ) 
            ) 
        );
    }

    function siteOEINISettings()
    {
        return array( 
            'name' => 'ezoe.ini', 
            'settings' => array( 
                'EditorSettings' => array( 
                    'SkinVariant' => 'silver' 
                ) 
            ) 
        );
    }
}
?>
