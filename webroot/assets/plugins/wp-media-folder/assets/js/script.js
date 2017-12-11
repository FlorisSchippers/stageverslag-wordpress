/** 
 * We developed this code with our hearts and passion.
 * @package wp-media-folder
 * @copyright Copyright (C) 2014 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */
var initOwnFilter, relCategoryFilter = {}, relFilterCategory = {}, currentCategory = 0, usedAttachmentsBrowser = null, page = null, wpmfNextIsGallery = false, wpmfcheckMimetype = false;
var wpmf_move_fi;
(function ($) {
    if (typeof ajaxurl == "undefined") {
        ajaxurl = wpmflang.ajaxurl;
    }
    
    $(document).ready(function () {
        if (typeof wpmflang == "undefined")
            return;
        // ---------  folder tree -------------------------------- 
        var options = {
            'root': '/',
            'showroot': wpmflang.media_folder,
            'onclick': function (elem, type, file) {},
            'oncheck': function (elem, checked, type, file) {},
            'usecheckboxes': true, //can be true files dirs or false
            'expandSpeed': 500,
            'collapseSpeed': 500,
            'expandEasing': null,
            'collapseEasing': null,
            'canselect': true
        };

        var methods = {
            init: function (o) {
                if ($(this).length == 0) {
                    return;
                }
                $wpmftreethis = $(this);
                $.extend(options, o);

                if (wpmflang.wpmf_role == 'administrator') {
                    if (options.showroot != '') {
                        $wpmftreethis.html('<ul class="jaofiletree"><li data-id="0" class="directory collapsed selected" data-group="' + wpmflang.wpmf_curent_userid + '"><a class="title-folder title-root" href="#" data-id="0" data-file="' + options.root + '" data-type="dir">' + options.showroot + '</a></li></ul>');
                    }
                    openfolder(options.root);
                } else {
                    if (wpmflang.wpmf_active_media == 1 && wpmflang.term_root_id) {
                        $wpmftreethis.html('<ul class="jaofiletree"><li  data-id="' + wpmflang.term_root_id + '"  class="directory collapsed selected"><a class="title-folder title-root" href="#" data-id="' + wpmflang.term_root_id + '" data-file="/' + wpmflang.term_root_username + '/" data-type="dir">' + wpmflang.term_root_username + '</a></li></ul>');
                        openfolder('/' + wpmflang.term_root_username + '/');
                    } else {
                        if (options.showroot != '') {
                            $wpmftreethis.html('<ul class="jaofiletree"><li  data-id="0"  class="directory collapsed selected"><a class="title-folder title-root" href="#" data-id="0" data-file="' + options.root + '" data-type="dir">' + options.showroot + '</a></li></ul>');
                        }
                        openfolder(options.root);
                    }
                }
            },
            open: function (dir) {
                openfolder(dir);
            },
            close: function (dir) {
                closedir(dir);
            },
            getchecked: function () {
                var list = new Array();
                var ik = 0;
                $wpmftreethis.find('input:checked + a').each(function () {
                    list[ik] = {
                        type: $(this).attr('data-type'),
                        file: $(this).attr('data-file')
                    }
                    ik++;
                });
                return list;
            },
            getselected: function () {
                var list = new Array();
                var ik = 0;
                $wpmftreethis.find('li.selected > a').each(function () {
                    list[ik] = {
                        type: $(this).attr('data-type'),
                        file: $(this).attr('data-file')
                    }
                    ik++;
                });
                return list;
            }
        };

        setSelectedFolder = function (selectedId) {
            var $currentFolder = $('[id^="__wp-uploader-id-"]:visible li.directory[data-id="' + selectedId + '"] > a');
            $currentFolder.parent().find('li').removeClass('selected');
            $currentFolder.parent().find('i.zmdi').removeClass('zmdi-folder-outline').addClass("zmdi-folder");
            $currentFolder.parent().addClass("selected");
            $currentFolder.parent().find(' > i.zmdi').removeClass("zmdi-folder").addClass("zmdi-folder-outline");
            return true;
        };

        openfolders = function (dirs, selectedId) {
            if (dirs.length == 1 && dirs[0] == 0) {
                setSelectedFolder(0);
                return true;
            }
            parent_id = dirs.shift();
            if (page == 'table') {
                var cdir = $('#jao div[data-id="' + parent_id + '"]').data('file');
            } else {
                var cdir = $('[id^="__wp-uploader-id-"]:visible #jao div[data-id="' + parent_id + '"]').data('file');
            }
            if (dirs.length === 0) {
                openfolder(cdir, function () {
                    setSelectedFolder(selectedId)
                });
                return true;
            }

            openfolder(cdir, function () {
                openfolders(dirs, selectedId);
            });
            return true;
        };

        openfolder = function (dir, callback) {
            if (typeof $wpmftreethis == "undefined")
                return;
            var id = $wpmftreethis.find('a[data-file="' + dir + '"]').data('id');
            if ($wpmftreethis.find('a[data-file="' + dir + '"]').parent().hasClass('expanded') || $wpmftreethis.find('a[data-file="' + dir + '"]').parent().hasClass('wait')) {
                if (typeof callback === 'function')
                    callback();
                return;
            }
            var ret;
            ret = $.ajax({
                method: 'POST',
                url: ajaxurl,
                data: {dir: dir, id: id, action: 'get_terms', 'wpmf_display_media': $('#wpmf-display-media-filters').val()},
                context: $wpmftreethis,
                dataType: 'json',
                beforeSend: function () {
                    this.find('a[data-file="' + dir + '"]').parent().addClass('wait');
                }
            }).done(function (datas) {
                selectedId = $("#wcat").find('option:selected').data('id') || 0;
                ret = '<ul class="jaofiletree" style="display: none">';
                for (ij = 0; ij < datas.length; ij++) {
                    if (datas[ij].type == 'dir') {
                        classe = 'directory collapsed';
                    } else {
                        classe = 'file ext_' + datas[ij].ext;
                    }

                    if (parseInt(datas[ij].id) === parseInt(selectedId)) {
                        classe += ' selected';
                    }

                    ret += '<li class="' + classe + '" data-id="' + datas[ij].id + '" data-parent_id="' + datas[ij].parent_id + '" data-group="' + datas[ij].term_group + '">';
                    if (datas[ij].count_child > 0) {
                        ret += '<div class="icon-open-close" data-id="' + datas[ij].id + '" data-parent_id="' + datas[ij].parent_id + '" data-file="' + dir + datas[ij].file + '/" data-type="' + datas[ij].type + '"></div>';
                    } else {
                        ret += '<div class="icon-open-close" data-id="' + datas[ij].id + '" data-parent_id="' + datas[ij].parent_id + '" data-file="' + dir + datas[ij].file + '/" data-type="' + datas[ij].type + '" style="opacity:0"></div>';
                    }

                    if (parseInt(datas[ij].id) === parseInt(selectedId)) {
                        ret += '<i class="zmdi zmdi-folder-outline"></i>';
                    } else {
                        ret += '<i class="zmdi zmdi-folder"></i>';
                    }

                    ret += '<a href="#" class="title-folder" data-id="' + datas[ij].id + '" data-parent_id="' + datas[ij].parent_id + '" data-file="' + dir + datas[ij].file + '/" data-type="' + datas[ij].type + '">' + datas[ij].file + '</a>';
                    ret += '</li>';
                }
                ret += '</ul>';

                this.find('a[data-file="' + dir + '"]').parent().removeClass('wait').removeClass('collapsed').addClass('expanded');

                this.find('a[data-file="' + dir + '"]').after(ret);
                this.find('a[data-file="' + dir + '"]').next().slideDown(options.expandSpeed, options.expandEasing,
                        function () {
                            $wpmftreethis.trigger('afteropen');
                            $wpmftreethis.trigger('afterupdate');
                            if (typeof callback === 'function')
                                callback();
                        });
                setevents();
                wpmfinitDroppable();

            }).done(function () {
                $wpmftreethis.trigger('afteropen');
                $wpmftreethis.trigger('afterupdate');
            });

        }

        closedir = function (dir) {
            if (typeof $wpmftreethis == "undefined")
                return;
            $wpmftreethis.find('a[data-file="' + dir + '"]').next().slideUp(options.collapseSpeed, options.collapseEasing, function () {
                $(this).remove();
            });

            $wpmftreethis.find('a[data-file="' + dir + '"]').parent().removeClass('expanded').addClass('collapsed');
            setevents();

            //Trigger custom event
            $wpmftreethis.trigger('afterclose');
            $wpmftreethis.trigger('afterupdate');
        };

        setevents = function () {
            $wpmftreethis = $('.wpmf_jao');
            $wpmftreethis.find('li a,li .icon-open-close').unbind('click');

            //Bind for collapse or expand elements
            $wpmftreethis.find('li.directory a').bind('click', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                if (page !== 'table') {
                    $("#jao").find('li').removeClass('selected');
                    $("#jao").find('i.zmdi').removeClass('zmdi-folder-outline').addClass("zmdi-folder");
                    $(this).parent().addClass("selected");
                    $(this).parent().find(' > i.zmdi').removeClass("zmdi-folder").addClass("zmdi-folder-outline");
                    $('.wpmf-categories [data-id="' + id + '"]').prop('selected', 'selected').change();
                    methods.open($(this).attr('data-file'));
                    $('.select_folder_id').val($(this).data('id'));
                } else {
                    $('.wpmf-categories [data-id="' + id + '"]').prop('selected', 'selected').change();
                }
            });

            $wpmftreethis.find('li.directory.collapsed .icon-open-close').bind('click', function (e) {
                e.preventDefault;
                methods.open($(this).attr('data-file'));
            });

            $wpmftreethis.find('li.directory.expanded .icon-open-close').bind('click', function (e) {
                e.preventDefault;
                methods.close($(this).attr('data-file'));
            });

        }

        $.fn.jaofiletree = function (method) {
            // Method calling logic
            if (methods[method]) {
                return methods[ method ].apply(this, Array.prototype.slice.call(arguments, 1));
            } else if (typeof method === 'object' || !method) {
                return methods.init.apply(this, arguments);
            } else {
                //error
            }
        };

// --------- end folder tree --------------------------------

        addFolder = function () {
            if (wpmflang.wpmf_role == 'administrator') {
                if (wpmflang.wpmf_selected_dmedia == 'yes') {
                    wpmf_checkbox_display_media = $('<div class="div_displaymedia"><input type="checkbox" class="wpmf-display-media" name="wpmf-display-media" checked style="margin:20px 10px 20px 15px;"><label>' + wpmflang.display_media + '</label></div>');
                } else {
                    wpmf_checkbox_display_media = $('<div class="div_displaymedia"><input type="checkbox" class="wpmf-display-media" name="wpmf-display-media" style="margin:20px 10px 20px 15px;"><label>' + wpmflang.display_media + '</label></div>');
                }
            } else {
                wpmf_checkbox_display_media = '';
            }

            if ($('[id^="__wp-uploader-id-"]:visible #addFolder').length === 0) {
                if (page !== 'table') {
                    btnNewFolder = $('<div id="addFolder" class="media-toolbar-third"><input type="button" placeholder="Recherche" class="button button-primary button-large addFolder" value="' + wpmflang.create_folder + '"></div>');
                    $('[id^="__wp-uploader-id-"]:visible .media-frame-content .media-toolbar-secondary').after(wpmf_checkbox_display_media);
                    $('[id^="__wp-uploader-id-"]:visible .media-frame-content .media-toolbar-secondary').after(btnNewFolder);
                } else {
                    btnNewFolder = $('<div id="addFolder" style="display: inline-block;"><input type="button" placeholder="Recherche" class="button button-primary button-large addFolder" value="' + wpmflang.create_folder + '" style="margin-top: -4px;"></div>');
                    $('.wp-filter .actions').after(btnNewFolder);
                    $('.upload-php .wp-filter').append(wpmf_checkbox_display_media);
                }

                btnNewFolder.click(function () {
                    name = prompt(wpmflang.promt, wpmflang.new_folder);
                    if (name !== '' && name != 'null') {
                        $.ajax({
                            type: "POST",
                            url: ajaxurl,
                            data: {
                                action: "add_folder",
                                name: name,
                                parent: $('select.wpmf-categories option:selected').data('id') | 0
                            },
                            success: function (response) {
                                if (typeof (response.term_id) !== 'undefined') {
                                    categoriesCount = $('select.wpmf-categories option').length - 1;
                                    relCategoryFilter[response.term_id] = String(categoriesCount + 1);
                                    relFilterCategory[categoriesCount + 1] = response.term_id;
                                    if (page !== 'table') {
                                        $('select.wpmf-categories option:selected').after('<option value="' + (categoriesCount + 1) + '" data-id="' + response.term_id + '" data-parent_id="' + response.parent + '">' + response.name + '</option>');
                                    } else {
                                        $('select.wpmf-categories option:selected').after('<option class="level-' + response.level + '" value="' + response.term_id + '" data-id="' + response.term_id + '" data-parent_id="' + response.parent + '">' + response.name + '</option>');
                                    }

                                    $('.wpmf-attachments-browser').append('<li class="wpmf-attachment" data-parent_id="' + response.parent + '" data-id="' + response.term_id + '">' +
                                            '<div class="wpmf-attachment-preview">' +
                                            '<i class="zmdi zmdi-folder"></i>' +
                                            '<div class="filename">' +
                                            '<div>' + response.name + '</div>' +
                                            '</div>' +
                                            '<i class="wpmficon-edit zmdi zmdi-edit"></i>' +
                                            '<i class="wpmficon-delete zmdi zmdi-delete"></i>' +
                                            '</div>' +
                                            '</li>'
                                            );

                                    //folder tree
                                    var dir_parent = $('li.directory.selected > a').data('file');
                                    var dir = dir_parent + response.name + '/';
                                    var ret = '<li class="directory collapsed" data-id="' + response.term_id + '" data-parent_id="' + response.parent + '">'
                                    ret += '<div class="icon-open-close" data-id="' + response.term_id + '" data-parent_id="' + response.parent + '" data-file="' + dir + '" data-type="dir"></div>';
                                    ret += '<i class="zmdi zmdi-folder"></i>';
                                    ret += '<a href="#" class="title-folder" data-id="' + response.term_id + '" data-parent_id="' + response.parent + '" data-file="' + dir + '" data-type="dir">' + response.name + '</a>';
                                    ret += '</li>';
                                    $('#jao').find('li[data-id="' + response.parent + '"] > .jaofiletree').append(ret);
                                    $('li.directory[data-id="' + response.parent + '"] .icon-open-close[data-id="' + response.parent + '"]').css({'opacity': 1});
                                    $('li.directory[data-id="' + response.term_id + '"] .icon-open-close').css({'opacity': 0});
                                    setevents();

                                    //Add element to the select list
                                    wpmflang.wpmf_categories[response.term_id] = {id: response.term_id, label: response.name, parent_id: response.parent, slug: response.slug};
                                    wpmflang.wpmf_categories_order[categoriesCount + 1] = response.term_id;

                                    if (page !== 'table') {
                                        initOwnFilter();
                                        if ($('.media-frame').hasClass('mode-select')) {
                                            $('select.wpmf-categories').hide();
                                        }
                                        $('select.wpmf-categories option[data-id="' + currentCategory + '"]').prop('selected', 'selected');
                                    }

                                    initDraggable();
                                    wpmfinitDroppable();
                                    bindAttachmentEvent();

                                    if (page != 'table') {
                                        $('[id^="__wp-uploader-id-"]:visible .wpmf-order-folder').change();
                                    } else {
                                        $('.wpmf-order-folder').change();
                                    }
                                } else {
                                    alert(wpmflang.alert_add);
                                }
                            }
                        });
                    }
                });
            }
        };

        initSelectFilter = function () {
            if (page !== 'table') {
                //set the id for each option
                $('select.wpmf-categories option').each(function () {
                    if ($(this).val() !== 0 && typeof (relFilterCategory[$(this).val()]) !== 'undefined' && typeof (wpmflang.wpmf_categories[relFilterCategory[$(this).val()]]) !== 'undefined') {
                        $(this).attr('data-id', wpmflang.wpmf_categories[relFilterCategory[$(this).val()]].id);
                        $(this).attr('data-parent_id', wpmflang.wpmf_categories[relFilterCategory[$(this).val()]].parent_id);
                        $(this).attr('data-group', wpmflang.wpmf_categories[relFilterCategory[$(this).val()]].term_group);
                    }
                });

                //bind the change event on select
                $('[id^="__wp-uploader-id-"]:visible select.wpmf-categories').bind('change', function () {
                    var id = $(this).find('option:selected').data('id');
                    changeCategory.call(this);
                });

                if ($('ul.attachments').length) {
                    $('ul.attachments').get(0).addEventListener("DOMNodeInserted", function () {
                        $('ul.attachments').trigger('change');
                    });
                }
            } else {
                //set the id for each option
                $('select.wpmf-categories option').each(function () {
                    if ($(this).val() !== 0 && typeof (wpmflang.wpmf_categories[$(this).val()]) !== 'undefined') {
                        $(this).attr('data-id', wpmflang.wpmf_categories[$(this).val()].id);
                        $(this).attr('data-parent_id', wpmflang.wpmf_categories[$(this).val()].parent_id);
                        $(this).attr('data-group', wpmflang.wpmf_categories[$(this).val()].term_group);
                    } else if ($(this).val() < 0) {
                        $(this).val(0);
                        $(this).attr('data-id', 0);
                        $(this).attr('data-parent_id', 0);
                    }
                });
                $('select.wpmf-categories').change(function () {
                    $('select.wpmf-categories').parents('form').submit();
                });
            }
        };

        initDraggable = function () {
            if (typeof jQuery.ui != "undefined" && $('.wpmf-attachments-browser .wpmf-attachment').length > 0) {
                $('.wpmf-attachments-browser .wpmf-attachment:not(.wpmf-attachment-back )').draggable({
                    revert: true,
                    distance: 10,
                    cursorAt: {top: 10, left: -10},
                    helper: function (e) {
                        helper = $(this).clone();
                        var name = helper.find('.filename div').text();
                        var data_id = helper.data('id');
                        var data_parentid = helper.data('parent_id');
                        var md_folder = '<li class="wpmf-attachmentdrag" data-id="' + data_id + '" data-parent_id="' + data_parentid + '">' +
                                '<div class="wpmf-attachment-preview">' +
                                '<i class="zmdi zmdi-folder"></i>' +
                                '<div class="filename">' +
                                '<div>' + name + '</div>' +
                                '</div>' +
                                '</div>' +
                                '</li>';
                        return md_folder;
                    },
                    drag: function () {
                        if (page == 'table') {
                            var $wpmf_ui = $('#jao');
                        } else {
                            var $wpmf_ui = $('[id^="__wp-uploader-id-"]:visible #jao');
                        }
                        if ($wpmf_ui.hasClass('ui-hoverClass')) {
                            $('.ui-draggable-dragging').addClass('wpmf_dragsmall').removeClass('wpmf_draglarge');
                            if (wpmflang.wpmf_pagenow != 'upload.php') {
                                $('[id^="__wp-uploader-id-"]:visible .title-folder.ui-hoverClass,[id^="__wp-uploader-id-"]:visible #jao').css({'cursor': 'url(' + wpmflang.wpmf_images_path + '/folder.png),auto'});
                            }
                        } else {
                            $('[id^="__wp-uploader-id-"]:visible .title-folder,[id^="__wp-uploader-id-"]:visible #jao').css({'cursor': 'default'});
                            $('.ui-draggable-dragging').removeClass('wpmf_dragsmall').addClass('wpmf_draglarge');
                        }
                    },
                    start: function (event, ui) {
                        var id = helper.data('id');
                        $('li.wpmf-attachment[data-id="' + id + '"]:not(".ui-draggable-dragging")').addClass('wpmfnoclick');
                        $('li.wpmf-attachment[data-id="' + id + '"]:not(".ui-draggable-dragging")').css({'opacity': '0.2'});
                        $('.ui-draggable-dragging').addClass('wpmf_dragsmall')
                    },
                    stop: function (event, ui) {
                        var id = helper.data('id');
                        $('li.wpmf-attachment[data-id="' + id + '"]:not(".ui-draggable-dragging")').removeClass('wpmfnoclick');
                        $('li.wpmf-attachment[data-id="' + id + '"]').css({'opacity': '1'});
                        if (wpmflang.wpmf_pagenow != 'upload.php') {
                            $('[id^="__wp-uploader-id-"]:visible #jao a.title-folder,[id^="__wp-uploader-id-"]:visible #jao').css({'cursor': 'default'});
                        }
                    }
                });
            }
        };

        refreshWpmf = function () {
            var parent = null;
            wrappers = $('.wpmf-attachments-wrapper');
            wrappers.each(function () {
                if ($(this).is(':visible')) {
                    parent = $(this).parents('[id^="__wp-uploader-id-"]').last();
                }
            });

            if (parent === null) {
                parent = $(document);
            }

            if ($(parent).find('.wpmf-attachments-wrapper:visible .wpmf-attachments-browser').length === 0) {
                if(wpmflang.wpmf_pagenow == 'upload.php'){
                    $(parent).find('[id^="__wp-uploader-id-"]:visible #wpcontent .wpmf-attachments-browser, [id^="__wp-uploader-id-"]:visible .wpmf-breadcrumb').remove();
                    //add the folders
                    $(parent).find('[id^="__wp-uploader-id-"]:visible #wpcontent ul.attachments').before('<ul class="wpmf-attachments-browser"></ul><div class="wpmf-clear"></div>');

                    //wrapall
                    $(parent).find('[id^="__wp-uploader-id-"]:visible #wpcontent .attachments-browser ul.attachments,[id^="__wp-uploader-id-"]:visible .wpmf-breadcrumb, [id^="__wp-uploader-id-"]:visible .attachments-browser .wpmf-attachments-browser,[id^="__wp-uploader-id-"]:visible .wpmf-clear').wrapAll('<div class="wpmf-attachments-wrapper"></div>');
                }else{
                    $(parent).find('[id^="__wp-uploader-id-"]:visible .wpmf-attachments-browser, [id^="__wp-uploader-id-"]:visible .wpmf-breadcrumb').remove();
                    //add the folders
                    $(parent).find('[id^="__wp-uploader-id-"]:visible ul.attachments').before('<ul class="wpmf-attachments-browser"></ul><div class="wpmf-clear"></div>');

                    //wrapall
                    $(parent).find('[id^="__wp-uploader-id-"]:visible .attachments-browser ul.attachments,[id^="__wp-uploader-id-"]:visible .wpmf-breadcrumb, [id^="__wp-uploader-id-"]:visible .attachments-browser .wpmf-attachments-browser,[id^="__wp-uploader-id-"]:visible .wpmf-clear').wrapAll('<div class="wpmf-attachments-wrapper"></div>');
                }

                if (wpmfNextIsGallery === true) {
                    wpmfNextIsGallery = false;
                    return;
                }
                wpmfNextIsGallery = false;

                //add folder creation button if not exists
                addFolder();

                //add the breadcrumb
                $(parent).find('[id^="__wp-uploader-id-"]:visible .wpmf-attachments-wrapper').prepend('<ul class="wpmf-breadcrumb"><li><a href="#" data-id="0">Files</a></li></ul>');

                initSelectFilter();

                //trigger the first selection
                $(parent).find('[id^="__wp-uploader-id-"]:visible .wpmf-categories option').prop('selected',null);
                $(parent).find('[id^="__wp-uploader-id-"]:visible .wpmf-categories option[value="'+relCategoryFilter[currentCategory]+'"]').prop('selected','selected');
                
                if (wpmflang.wpmf_active_media == 1 && wpmflang.wpmf_role != 'administrator' && page != 'table' && wpmflang.term_root_id) {
                    $(parent).find('[id^="__wp-uploader-id-"]:visible select.wpmf-categories option[data-id="' + wpmflang.term_root_id + '"]').prop('selected', true).change();
                } else {
                    $(parent).find('[id^="__wp-uploader-id-"]:visible .wpmf-categories').change();
                }
            }
        };

        initAttachments = function () {

            if (wpmfcheckMimetype === true)
                return;
            if (typeof jQuery.ui != "undefined" && $('ul.attachments .attachment').length > 0) {
                $('ul.attachments .attachment:not(.attachment.uploading)').draggable({
                    revert: true,
                    cursorAt: {top: 10, left: 0},
                    helper: function (e) {
                        var elementsIds = [];
                        var elements = $.merge($(this), $('.wpmf-attachments-wrapper .attachments .attachment.selected').not(this));

                        //attach selected elements data-id to the helper
                        elements.each(function () {
                            elementsIds.push($(this).data('id'));
                        });
                        helper = $(this).clone();
                        helper.append('<span class="draggableNumber">' + elements.length + '</<span>');
                        helper.data('wpmfElementsIds', elementsIds.join());
                        return helper;
                    },
                    appendTo: ".wpmf-attachments-wrapper",
                    drag: function () {
                        if (page == 'table') {
                            var $wpmf_ui = $('#jao');
                        } else {
                            var $wpmf_ui = $('[id^="__wp-uploader-id-"]:visible #jao');
                        }
                        if ($wpmf_ui.hasClass('ui-hoverClass')) {
                            $('.ui-draggable-dragging').addClass('wpmf_dragsmall').removeClass('wpmf_draglarge').width(30).height(30);
                            $('[id^="__wp-uploader-id-"]:visible .ui-draggable-dragging .check').css({'opacity': '0'});
                            if (wpmflang.wpmf_pagenow != 'upload.php') {
                                if ($(window).width() <= 1600) {
                                    $('[id^="__wp-uploader-id-"]:visible .title-folder.ui-hoverClass,[id^="__wp-uploader-id-"]:visible #jao').css({'cursor': 'url(' + wpmflang.wpmf_images_path + '/picture.png),auto'});
                                } else {
                                    $('[id^="__wp-uploader-id-"]:visible .title-folder.ui-hoverClass,[id^="__wp-uploader-id-"]:visible #jao').css({'cursor': 'url(' + wpmflang.wpmf_images_path + '/picture1.png),auto'});
                                }
                            } else {
                                $('[id^="__wp-uploader-id-"]:visible .ui-draggable-dragging').css({'box-shadow': 'none'});
                            }
                        } else {
                            $('[id^="__wp-uploader-id-"]:visible .ui-draggable-dragging .check').css({'opacity': '1'});
                            if (wpmflang.wpmf_pagenow != 'upload.php') {
                                $('[id^="__wp-uploader-id-"]:visible .title-folder,[id^="__wp-uploader-id-"]:visible #jao').css({'cursor': 'default'});
                            } else {
                                $('[id^="__wp-uploader-id-"]:visible .ui-draggable-dragging').css({'box-shadow': 'inset 0 0 0 3px #f1f1f1, inset 0 0 0 7px #1e8cbe'});
                            }
                            $('.ui-draggable-dragging').removeClass('wpmf_dragsmall').addClass('wpmf_draglarge').width($('.attachments-browser .attachment').width()).height($('.attachments-browser .attachment').height());
                        }
                    },
                    start: function (event, ui) {
                        $('.ui-draggable-dragging').addClass('wpmf_dragsmall').removeClass('wpmf_draglarge');
                        var elementsIds = ui.helper.data('wpmfElementsIds').split(',');
                        $(elementsIds).each(function (index, value) {
                            $('.wpmf-attachments-wrapper .attachments .attachment[data-id="' + value + '"]').css('visibility', 'hidden');
                        });
                    },
                    stop: function (event, ui) {
                        if (wpmflang.wpmf_pagenow != 'upload.php') {
                            $('[id^="__wp-uploader-id-"]:visible .attachment .check').css({'opacity': '1'});
                            $('[id^="__wp-uploader-id-"]:visible #jao a.title-folder,[id^="__wp-uploader-id-"]:visible #jao').css({'cursor': 'default'});
                        }
                        var elementsIds = ui.helper.data('wpmfElementsIds').split(',');
                        $(elementsIds).each(function (index, value) {
                            $('.wpmf-attachments-wrapper .attachments .attachment[data-id="' + value + '"]').css('visibility', 'visible');
                        });
                    }
                });
            }


            if (wpmflang.useorder && wpmflang.useorder == 1) {
                if (wpmflang.wpmf_pagenow == 'upload.php') {
                    if (wpmflang.wpmfview == 'small') {
                        $('.wpmf_smallview').addClass('active').removeClass('noactive');
                        $('body').addClass('smallview_open').removeClass('smallview_close');
                        $('.view-switch.media-grid-view-switch > a').removeClass('current');
                    }
                } else {
                    $('.wpmf_viewsmall').unbind('click').bind('click', function () {
                        if ($(this).hasClass('noactive')) {
                            $(this).addClass('active').removeClass('noactive');
                            $('.wpmfview-grid').addClass('noactive').removeClass('active');
                            $('body').addClass('smallview_open').removeClass('smallview_close');
                        }
                    });

                    $('.wpmfview-grid').unbind('click').bind('click', function () {
                        if ($(this).hasClass('noactive')) {
                            $(this).addClass('active').removeClass('noactive');
                            $('.wpmf_viewsmall').addClass('noactive').removeClass('active');
                            $('body').addClass('smallview_close').removeClass('smallview_open');
                        }
                    });
                }
            }
        };

        wpmfinitDroppable = function () {
            if (page == 'table') {
                var $wpmfdrop = $('.wpmf-attachments-browser .wpmf-attachment ,.directory a.title-folder,#jao');
            } else {
                var $wpmfdrop = $('.wpmf-attachments-browser .wpmf-attachment ,.directory a.title-folder,[id^="__wp-uploader-id-"]:visible #jao');
            }

            if (typeof jQuery.ui != "undefined" && $wpmfdrop.length > 0) {
                $wpmfdrop.droppable({
                    hoverClass: "ui-hoverClass",
                    drop: function (event, ui) {
                        if ($(this).hasClass('title-folder')) {
                            if ($(ui.helper[0]).hasClass('wpmf_draglarge'))
                                return;
                        }

                        if ($(this).attr('id') == 'jao') {
                            return;
                        } else {
                            if ($(ui.draggable).hasClass('wpmf-attachment')) {
                                var curentID = $(ui.draggable[0]).data('id');
                                var curentParentID = $(ui.draggable[0]).data('parent_id');
                                if ($(this).data('id') != curentID && $(this).data('id') != curentParentID && $(this).data('parent_id') != curentID) {
                                    //case folder dropped on folder
                                    id_category = $(this).data('id');
                                    id = $(ui.draggable[0]).data('id');
                                    parent_id = $('.wpmf-categories option:selected').data('id');

                                    var name = $(ui.draggable[0]).find('.filename div').html();
                                    currentCategory = $('select.wpmf-categories option:selected').attr('data-id');
                                    $(ui.draggable[0]).hide();
                                    $('.wpmf-attachmentdrag.ui-draggable-dragging').hide();
                                    $.ajax({
                                        type: "POST",
                                        url: ajaxurl,
                                        data: {
                                            action: "move_folder",
                                            id: id,
                                            name: name,
                                            id_category: id_category,
                                            parent_id: parent_id
                                        },
                                        success: function (response) {
                                            if (response.status === true) {
                                                //update categories array
                                                wpmflang.wpmf_categories[id].parent_id = id_category;
                                                wpmflang.wpmf_categories[id].depth = wpmflang.wpmf_categories[id_category].depth + 1;
                                                incrDepth = function (parent) {
                                                    wpmflang.wpmf_categories.each(function (index, value) {
                                                        if (wpmflang.wpmf_categories[index].parent_id == parent) {
                                                            wpmflang.wpmf_categories[index].depth++;
                                                            incrDepth(wpmflang.wpmf_categories[index]);
                                                        }
                                                    });
                                                };
                                                var dir_parent = $('li.directory a[data-id="' + id_category + '"]').data('file');
                                                var dir = dir_parent + name + '/';
                                                $('.directory[data-id="' + id + '"]').remove();
                                                var ret = '<li class="directory collapsed" data-id="' + id + '" data-parent_id="' + wpmflang.wpmf_categories[id].parent_id + '">';
                                                ret += '<div class="icon-open-close" data-id="' + id + '" data-parent_id="' + wpmflang.wpmf_categories[id].parent_id + '" data-file="' + dir + '" data-type="dir"></div>';
                                                ret += '<i class="zmdi zmdi-folder"></i>';
                                                ret += '<a href="#" class="title-folder" data-id="' + id + '" data-parent_id="' + wpmflang.wpmf_categories[id].parent_id + '" data-file="' + dir + '" data-type="dir">' + wpmflang.wpmf_categories[id].label + '</a>';
                                                ret += '</li>';
                                                $('#jao').find('li[data-parent_id="' + wpmflang.wpmf_categories[id].parent_id + '"]').parent().append(ret);

                                                changeOpenStatus(id, (response.count_id > 0));
                                                changeOpenStatus(id_category, (response.id_category > 0));
                                                changeOpenStatus(parent_id, (response.parent_id > 0));
                                                setevents();

                                                //move item in the option list
                                                if (page !== 'table') {
                                                    var item = $('.wpmf-categories option[value="' + relCategoryFilter[id] + '"]').remove();
                                                    var afterItem = $('.wpmf-categories option[value="' + relCategoryFilter[id_category] + '"]');
                                                } else {
                                                    var item = $('.wpmf-categories option[value="' + id + '"]').remove();
                                                    var afterItem = $('.wpmf-categories option[value="' + id_category + '"]');
                                                }
                                                currentDepth = wpmflang.wpmf_categories[afterItem.data('id')].depth
                                                while (afterItem.next().lenght > 0 && wpmflang.wpmf_categories[afterItem.next().data('id')].depth !== currentDepth) {
                                                    afterItem = afterItem.next();
                                                }
                                                afterItem.after(item);

                                                //remove item in the attachment list
                                                $('.wpmf-attachment[data-id="' + id + '"]').remove();
                                                if (page != 'table') {
                                                    initOwnFilter();
                                                    if ($('.media-frame').hasClass('mode-select')) {
                                                        $('select.wpmf-categories').hide();
                                                    }
                                                }

                                                //reselect current category
                                                $('select.wpmf-categories option').prop('selected', null);
                                                $('select.wpmf-categories option[data-id="' + currentCategory + '"]').prop('selected', 'selected');

                                            } else {
                                                if (response.wrong == undefined) {
                                                    alert(wpmflang.alert_add);
                                                }
                                            }
                                        }
                                    });
                                }
                            } else {
                                //case file drop
                                id_category = $(this).data('id');

                                var elementsIds = ui.helper.data('wpmfElementsIds');
                                if (page != 'table') {
                                    if ($(this).hasClass('wpmf-attachment')) {
                                        $('.ui-draggable-dragging').css({'width': '40px', 'height': '40px', 'transition-duration': '0.5s'}).fadeOut(500);
                                        $('.draggableNumber').css({'padding': '4px 6px', 'transition-duration': '0.5s'});
                                        var wpmf_time = 500;
                                    } else {
                                        $('.ui-draggable-dragging').hide();
                                        var wpmf_time = 0;
                                    }
                                } else {
                                    $('.ui-draggable-dragging').hide();
                                    var wpmf_time = 0;
                                }

                                if (elementsIds != undefined) {
                                    $(elementsIds.split(',')).each(function () {
                                        $('li.attachment[data-id="' + this + '"]:not(.ui-draggable-dragging)').hide();
                                    });
                                }

                                setTimeout(function () {
                                    id_attachment = $(ui.draggable[0]).data('id');

                                    $.ajax({
                                        type: "POST",
                                        url: ajaxurl,
                                        data: {
                                            action: "move_file",
                                            ids: elementsIds,
                                            id_category: id_category
                                        },
                                        success: function (response) {
                                            if (response == true) {
                                                if(id_category != 0){
                                                    var countfiles = $('.wpmf-attachment[data-id="'+ id_category +'"] .wpmfcountfiles').html();
                                                    var new_countfiles = parseInt(countfiles) + elementsIds.split(',').length;
                                                    $('.wpmf-attachment[data-id="'+ id_category +'"] .wpmfcountfiles').html(new_countfiles);
                                                }
                                                if (page !== 'table') {

                                                    if (wp.media.frame.content.get() !== null) {
                                                        wp.media.frame.content.get().collection.props.set({ignore: (+new Date())});
                                                        wp.media.frame.content.get().options.selection.reset();
                                                    } else {
                                                        wp.media.frame.library.props.set({ignore: (+new Date())});
                                                        if (wpmflang.wpmf_pagenow == 'upload.php') {
                                                            wpmf_move_fi.controller.state().get('selection').reset();
                                                        }
                                                    }
                                                } else {
                                                    $(elementsIds.split(',')).each(function () {
                                                        $('#the-list #post-' + this).hide();
                                                    });
                                                    $('#the-list input[name="media[]"]').prop('checked', false);
                                                }
                                                $('.wpmf-move').removeClass('selected');

                                                $.ajax({
                                                    type: "POST",
                                                    url: ajaxurl,
                                                    data: {
                                                        action: "move_attachment",
                                                        ids: elementsIds,
                                                        id_category: id_category
                                                    },
                                                });
                                            }

                                        }
                                    });
                                }, wpmf_time)


                            }
                        }
                    }
                });
            }
        };

        changeOpenStatus = function (id, status) {
            if (status) {
                $('#jao').find('li.directory[data-id="' + id + '"] > .icon-open-close').css({'opacity': 1});
            } else {
                $('#jao').find('li.directory[data-id="' + id + '"] > .icon-open-close').css({'opacity': 0});
            }

        }

        changeCategory = function () {
            if (wpmflang.wpmf_pagenow == 'upload.php' && page != 'table') {
                if ($('.media-frame.mode-select .select-mode-toggle-button').length != 0) {
                    $('.media-frame.mode-select .select-mode-toggle-button').click();
                }
            }

            if ($(window).width() <= 1024) {
                if ($('[id^="__wp-uploader-id-"]:visible .wpmf-show-hide').length === 0) {
                    var wpmf_shfolder = '<div class="wpmf-show-hide"><div class="wpmf_sh wpmf-sh-folder wpmf-hide-folder"><label>Hide folder</label><i class="wpmf-arrow-folder dashicons dashicons-arrow-up"></i></div><div class="wpmf_sh wpmf-toolbar wpmf-hide-toolbar"><label>Hide toolbar</label><i class="wpmf-arrow-toolbar dashicons dashicons-arrow-up"></i></div></div>';
                    $('[id^="__wp-uploader-id-"]:visible .media-frame-content .media-toolbar').append(wpmf_shfolder);
                    $('.upload-php #posts-filter .wp-filter').append(wpmf_shfolder);
                }
            }


            if (wpmflang.useorder && wpmflang.useorder == 1) {

                if (wpmflang.wpmf_pagenow == 'upload.php') {
                    if ($('.wpmf_smallview').length == 0) {
                        $('.upload-php .view-switch').append('<a><i class="wpmf_smallview noactive zmdi zmdi-apps" title="' + wpmflang.smallview + '"></i></a>');
                    }

                    if (page == 'table') {
                        if (typeof wpmflang.wpmfcount_pdf != "undefined" && typeof wpmflang.wpmfcount_zip != "undefined" && typeof wpmflang.wpmf_file != "undefined") {
                            var wpmfoption = '<option data-filetype="pdf" value="wpmf-pdf">' + wpmflang.pdf + ' (' + wpmflang.wpmfcount_pdf + ')</option>';
                            wpmfoption += '<option data-filetype="zip" value="wpmf-zip">' + wpmflang.zip + ' (' + wpmflang.wpmfcount_zip + ')</option>';
                            wpmfoption += '<option data-filetype="other" value="wpmf-other">' + wpmflang.other + '</option>';
                            $('select[name="attachment-filter"] option[value="detached"]').before(wpmfoption);

                            if (wpmflang.wpmf_file != '') {
                                $('select[name="attachment-filter"] option[value="' + wpmflang.wpmf_file + '"]').prop('selected', true);
                            }
                        }
                    }
                } else {
                    if ($('[id^="__wp-uploader-id-"]:visible .wpmf_viewsmall').length == 0) {
                        if ($('body').hasClass('smallview_open')) {
                            var wpmfsmall_clas = 'active';
                            var wpmfviewdefault = 'noactive';
                        } else {
                            var wpmfsmall_clas = 'noactive';
                            var wpmfviewdefault = 'active';
                        }

                        var wpmfview = '<div class="viewwpmf"><a class="wpmfview-grid ' + wpmfviewdefault + '"></a>';
                        wpmfview += '<a style="float:left;margin-right:4px;"><i class="wpmf_viewsmall ' + wpmfsmall_clas + ' zmdi zmdi-apps" title="' + wpmflang.smallview + '"></i></a></div>';
                        $('[id^="__wp-uploader-id-"]:visible select.attachment-mimetype:not(.upload-php select.attachment-mimetype)').before(wpmfview);
                    }
                }

                if (wpmflang.wpmf_pagenow == 'upload.php') {
                    if (page != 'table') {
                        if ($('[id^="__wp-uploader-id-"]:visible .wpmf_br').length === 0) {
                            $('[id^="__wp-uploader-id-"]:visible .wpmf-order-folder').before('<br class="wpmf_br">');
                        }
                    }
                }

                if (typeof wpmflang.order_f != 'undefined' && wpmflang.order_f != 'all') {
                    $('.wpmf-order-folder option[value="' + wpmflang.order_f + '"]').prop('selected', true);
                }
            }
            if (wpmflang.usegellery == 1) {
                if ($('.btn-selectall').length === 0) {
                    btnSelectAll = "<a href='#' class='button media-button button-primary button-large btn-selectall'>" + wpmflang.create_gallery_folder + "</a>";
                    $('.button.media-button.button-primary.button-large.media-button-gallery').before(btnSelectAll);
                }

                if ($('.btn-selectall1').length === 0) {
                    btnSelectAll1 = "<a href='#' class='button media-button button-primary button-large btn-selectall1'>" + wpmflang.create_gallery_folder + "</a>";
                    $('.button.media-button.button-primary.button-large.media-button-insert').before(btnSelectAll1);
                }
            }
            if ($('.select_folder_id').length === 0) {
                $('body').append('<input type="hidden" class="select_folder_id" value="0">');
            }

            categoriesCount = $('select.wpmf-categories option').length - 1;
            //unselect items
            if (wpmflang.wpmf_pagenow == 'upload.php') {
                if (typeof (wp.media) !== 'undefined' && typeof (wp.media.frame) !== 'undefined' && wp.media.frame.content.get() !== null) {
                    wp.media.frame.content.get().options.selection.reset();
                }
            }

            if (page != 'table') {
                var wpmfattachmentsbrowser = $('[id^="__wp-uploader-id-"]:visible .wpmf-attachments-browser');
            } else {
                var wpmfattachmentsbrowser = $('.wpmf-attachments-browser');
            }

            wpmfattachmentsbrowser.html(null);

            selectedId = $(this).find('option:selected').data('id') || 0;
            selectedParentId = $(this).find('option:selected').data('parent_id') || 0;
            //folder tree
            var menu_left = '<div id="jao" class="wpmf_jao"></div>';
            if ($('[id^="__wp-uploader-id-"]:visible').find('#jao').length === 0) {
                if (page == 'table') {
                    $('.wpmf-attachments-browser').before(menu_left);
                    $('.wpmf-attachments-wrapper:visible').find('#jao').jaofiletree({
                        onclick: function (elem, type, file) {}
                    });
                } else {
                    if ($('[id^="__wp-uploader-id-"]:visible .media-frame.hide-menu').length === 0) {
                        if ($('.media-frame-menu .media-menu #jao').length == 0) {
                            $('[id^="__wp-uploader-id-"]:visible .media-frame-menu .media-menu:not([id^="__wp-uploader-id-"]:visible .media-frame.hide-menu .media-frame-menu .media-menu)').append(menu_left);
                            if ($('[id^="__wp-uploader-id-"]:visible .media-menu').find('#jao').length > 0) {
                                $('[id^="__wp-uploader-id-"]:visible .media-menu').find('#jao').jaofiletree({
                                    onclick: function (elem, type, file) {}
                                });
                            }
                        }

                    } else {
                        if ($('[id^="__wp-uploader-id-"]:visible .attachments-browser .wpmf-attachments-browser').find('#jao').length === 0) {
                            $('[id^="__wp-uploader-id-"]:visible .attachments-browser .wpmf-attachments-browser').before(menu_left);
                            if ($('[id^="__wp-uploader-id-"]:visible .wpmf-attachments-wrapper').find('#jao').length > 0) {
                                $('[id^="__wp-uploader-id-"]:visible .wpmf-attachments-wrapper').find('#jao').jaofiletree({
                                    onclick: function (elem, type, file) {}
                                });
                            }
                        }
                    }
                }


                $('#jao').bind('afteropen', function () {
                    jQuery('#debugcontent').prepend('A folder has been opened<br/>');
                });
                $('#jao').bind('afterclose', function () {
                    jQuery('#debugcontent').prepend('A folder has been closed<br/>');
                });

                //open folder tree deeper
                if (page === 'table') {
                    var initTree = false;
                    jQuery('#jao').bind('afterupdate', function () {
                        if (!initTree) {

                            openfolders(wpmflang.parents_array, selectedId);
                            initTree = true;

                        }
                    });
                }
            }

            var wpmf_back = '<li class="wpmf-attachment wpmf-attachment-back" data-id="' + selectedParentId + '">' +
                    '<div class="wpmf-attachment-preview">' +
                    '<i class="zmdi zmdi-long-arrow-return"></i>' +
                    '<div class="filename">' +
                    '<div>' + wpmflang.back + '</div>' +
                    '</div>' +
                    '</div>' +
                    '</li>';

            if (wpmflang.term_root_id && wpmflang.wpmf_active_media == 1 && wpmflang.wpmf_role != 'administrator') {
                if (selectedId !== parseInt(wpmflang.term_root_id)) {
                    wpmfattachmentsbrowser.append(wpmf_back);
                }
            } else {
                if (selectedId !== 0) {
                    wpmfattachmentsbrowser.append(wpmf_back);
                }
            }

            $('.select_folder_id').val(selectedId);
            //save the current folder 
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    action: "change_folder",
                    id: $('.select_folder_id').val()
                },
                success: function (res) {
                    
                    $.each(res.terms, function () {
                        if (typeof res.option_bgfolder[this.term_id] != "undefined") {
                            var bg = res.option_bgfolder[this.term_id][1];
                            var style = 'background-image: url(' + bg + ') !important;background-size: 100% 100% !important;'
                            var md_folder = '';
                        } else {
                            var style = '';
                            var md_folder = '<i class="zmdi zmdi-folder"></i>';
                        }
                        if(typeof wpmflang.option_countfiles != "undefined" && wpmflang.option_countfiles == 1){
                            var countfiles = '<div class="wpmfcountfiles">'+ res.countfiles[this.term_id] +'</div>';
                        }else{
                            var countfiles = '';
                        }
                        var wpmf_folder = '<li class="wpmf-attachment" data-parent_id="' + this.parent + '" data-id="' + this.term_id + '">' +
                                '<div class="wpmf-attachment-preview" style="' + style + '">' +
                                md_folder + countfiles +
                                '<div class="filename">' +
                                '<div>' + this.name + '</div>' +
                                '</div>' +
                                '<i class="wpmficon-edit zmdi zmdi-edit"></i>' +
                                '<i class="wpmficon-delete zmdi zmdi-delete"></i>' +
                                '</div>' +
                                '</li>';
                        if (this.slug !== '') {
                            if ($('.wpmf-display-media').length > 0 && $('.wpmf-display-media').attr('checked') == 'checked') {
                                if (wpmflang.wpmf_role == 'administrator' && this.term_group == wpmflang.wpmf_curent_userid) {
                                    wpmfattachmentsbrowser.append(wpmf_folder);
                                }
                            } else {
                                wpmfattachmentsbrowser.append(wpmf_folder);
                            }
                        }
                    });

                    initAttachments();
                    wpmfinitDroppable();
                    initDraggable();
                    bindAttachmentEvent();

                    if (wpmflang.wpmf_role == 'administrator') {
                        if ($('.wpmf-display-media').attr('checked') == 'checked') {
                            $('.wpmf-categories option:not(.wpmf-categories option[data-id="0"])').hide();
                            $.each(res.id1, function (key, value) {
                                $('.wpmf-categories option[data-id="' + value + '"]').show();
                            });
                        } else {
                            $('.wpmf-categories option').show();
                            $('.jaofiletree .directory').show();
                        }
                    } else if (wpmflang.wpmf_role != 'administrator' && wpmflang.wpmf_active_media == 1) {
                        if (wpmflang.term_root_id) {
                            $('#wcat option:not(#wcat option:first-child)').each(function () {
                                if ($(this).data('id') == undefined || $(this).data('id') == wpmflang.term_root_id) {
                                    $(this).remove();
                                }
                            });

                            $('.wpmf-categories option').each(function () {
                                if ($(this).data('parent_id') == 0 && $(this).data('id') != wpmflang.term_root_id) {
                                    $(this).remove();
                                }
                            });
                        } else {
                            $('#wcat option').hide();
                            $('#jao .directory >a').each(function () {
                                if (page == 'table') {
                                    $('#wcat option[data-id="' + $(this).data('id') + '"]').show();
                                }
                            });
                        }
                    }
                }
            });

            if (page !== 'table') {
                currentCategory = relFilterCategory[$(this).val()];
            } else {
                currentCategory = $(this).val();
            }

            if (currentCategory == null || currentCategory < 0)
                currentCategory = 0;
            //alter breadcrumb
            if (page == 'table') {
                var wpmfbreadcrumb = $('.wpmf-breadcrumb');
            } else {
                var wpmfbreadcrumb = $('[id^="__wp-uploader-id-"]:visible .wpmf-breadcrumb');
            }

            wpmfbreadcrumb.html(null);

            bcat = wpmflang.wpmf_categories[currentCategory];
            if (wpmflang.wpmf_categories[currentCategory] == undefined) {
                bcat = wpmflang.wpmf_categories[wpmflang.term_root_id];
            } else {
                bcat = wpmflang.wpmf_categories[currentCategory];
            }
            breadcrumb = '';
            while (bcat.parent_id != 0) {
                breadcrumb = '<li>&nbsp;&nbsp;/&nbsp;&nbsp;<a href="#" data-id="' + wpmflang.wpmf_categories[bcat.id].id + '">' + wpmflang.wpmf_categories[bcat.id].label + '</a></li>' + breadcrumb;
                bcat = wpmflang.wpmf_categories[wpmflang.wpmf_categories[bcat.id].parent_id];
            }
            if (bcat.id != 0) {
                breadcrumb = '<li><a href="#" data-id="' + wpmflang.wpmf_categories[bcat.id].id + '">' + wpmflang.wpmf_categories[bcat.id].label + '</a></li>' + breadcrumb;
            }
            breadcrumb = '<li>' + wpmflang.youarehere + '&nbsp;&nbsp;:<a href="#" data-id="0">&nbsp;&nbsp;' + wpmflang.home + '&nbsp;&nbsp;</a>/&nbsp;&nbsp;</li>' + breadcrumb;
            wpmfbreadcrumb.prepend(breadcrumb);
            $('.wpmf-breadcrumb a').click(function () {
                if (page != 'table') {
                    $('[id^="__wp-uploader-id-"]:visible .wpmf-categories [data-id="' + wpmflang.wpmf_categories[$(this).data('id')].id + '"]').prop('selected', 'selected').change();
                } else {
                    $('.wpmf-categories [data-id="' + wpmflang.wpmf_categories[$(this).data('id')].id + '"]').prop('selected', 'selected').change();
                }
            });

            if (page !== 'table') {
                $('ul.attachments').unbind('change').bind('change', function () {
                    initAttachments();
                });
            } else {
                $('input[name="media[]"]').change(function () {
                    if ($(this).is(':checked')) {
                        $(this).parents('tr').find('.wpmf-move').addClass('selected');
                    } else {
                        $(this).parents('tr').find('.wpmf-move').removeClass('selected');
                    }
                });

                if (typeof jQuery.ui != "undefined" && $('.wpmf-move').length > 0) {
                    $('.wpmf-move').draggable({
                        revert: true,
                        helper: function (e) {
                            var elementsIds = [];
                            var elements = $.merge($(this).parents('tr').find('input[name="media[]"]'), $('#the-list input[name="media[]"]:checked').not($(this).parents('tr').find('input[name="media[]"]')));
                            //attach selected elements data-id to the helper
                            elements.each(function () {
                                elementsIds.push($(this).val());
                            });
                            helper = $(this).clone();
                            helper.append('<span class="draggableNumber">' + elements.length + '</<span>');
                            helper.data('wpmfElementsIds', elementsIds.join());
                            return helper;
                        },
                        appendTo: ".wpmf-attachments-wrapper",
                        start: function (event, ui) {
                            var elementsIds = ui.helper.data('wpmfElementsIds').split(',');
                            $(elementsIds).each(function (index, value) {
                                $('#post-' + value + '').css('opacity', '0.2');
                            });
                        },
                        stop: function (event, ui) {
                            var elementsIds = ui.helper.data('wpmfElementsIds').split(',');
                            $(elementsIds).each(function (index, value) {
                                $('#post-' + value + '').css('opacity', '1');
                            });
                        }
                    });
                }
            }
            bindAttachmentEvent();

            if (page !== 'table') {
                bcat = wpmflang.wpmf_categories[currentCategory];
                var dirs = [];
                dirs.push(bcat.id);
                if (wpmflang.term_root_id && wpmflang.wpmf_active_media == 1 && wpmflang.wpmf_role != 'administrator') {
                    var root_id = parseInt(wpmflang.term_root_id);
                } else {
                    var root_id = 0;
                }

                if (wpmflang.wpmf_active_media == 1 && wpmflang.wpmf_role != 'administrator') {
                    $('.jaofiletree li').removeClass('selected');
                    $('.directory').find('.zmdi').removeClass('zmdi-folder-outline').addClass('zmdi-folder');
                    $('.jaofiletree li[data-id="' + selectedId + '"]').addClass('selected');
                } else {
                    while (bcat.parent_id != root_id) {
                        bcat = wpmflang.wpmf_categories[wpmflang.wpmf_categories[bcat.id].parent_id];
                        dirs.unshift(bcat.id);
                    }
                }
                if ($('#jao').length > 0) {
                    openfolders(dirs, currentCategory);
                }
            } else {

            }

            $('body:not(.upload-php) .media-frame').find('.uploader-inline').css('display', 'none');
            if (wpmflang.enhanced_media_plugin) {
                var w_slider = $('.upload-php .media-sidebar').width();
                var w_slider_padding_left = parseFloat($('.upload-php .media-sidebar').css('padding-left'));
                var w_slider_padding_right = parseFloat($('.upload-php .media-sidebar').css('padding-right'));
                var w_content = $('.upload-php .media-frame-content').width();
                var w_attachments = w_content - w_slider - w_slider_padding_left - w_slider_padding_right;
                $('.upload-php .wpmf-attachments-wrapper').css({'width': w_attachments + 'px', 'height': '100%', 'overflow': 'auto'});
                $('.upload-php .wpmf-attachments-browser').css({'width': '76%'});
            }
        };

        //bind the click event on folders
        bindAttachmentEvent = function () {
            $('.button-link.edit-selection').on('click',function(){
                wpmfNextIsGallery = true;
            });
            $('.wpmf-sh-folder').unbind('click').bind('click', function (e) {
                var $this = $(this);
                if ($this.hasClass('wpmf-hide-folder')) {
                    $('.wpmf-attachments-browser,#jao').hide();
                    $this.removeClass('wpmf-hide-folder').addClass('wpmf-show-folder');
                    $this.find('label').text('Show folder');
                    $this.find('.wpmf-arrow-folder').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
                } else {
                    $('.wpmf-attachments-browser,#jao').show();
                    $this.removeClass('wpmf-show-folder').addClass('wpmf-hide-folder');
                    $this.find('label').text('Hide folder');
                    $this.find('.wpmf-arrow-folder').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
                }
            });

            $('.wpmf-toolbar').unbind('click').bind('click', function (e) {
                var $this = $(this);
                if ($this.hasClass('wpmf-hide-toolbar')) {
                    $('.attachments-browser .media-toolbar').addClass('wpmf-hide').removeClass('wpmf-show');
                    $('.wp-filter').addClass('wpmf-hide').removeClass('wpmf-show');
                    $this.removeClass('wpmf-hide-toolbar').addClass('wpmf-show-toolbar');
                    $this.find('label').text('Show toolbar');
                    $this.find('.wpmf-arrow-toolbar').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
                    $('.media-frame .wpmf-attachments-wrapper').css({'top': '25px'});
                } else {
                    $('.attachments-browser .media-toolbar').addClass('wpmf-show').removeClass('wpmf-hide');
                    $('.wp-filter').addClass('wpmf-show').removeClass('wpmf-hide');
                    $this.removeClass('wpmf-show-toolbar').addClass('wpmf-hide-toolbar');
                    $this.find('label').text('Hide toolbar');
                    $this.find('.wpmf-arrow-toolbar').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');

                    if ($(window).width() <= 320) {
                        $('.media-frame .wpmf-attachments-wrapper').css({'top': '330px'});
                    } else {
                        $('.media-frame .wpmf-attachments-wrapper').css({'top': '250px'});
                    }
                }
            });

            $('.wpmf_smallview').parent().unbind('click').bind('click', function () {
                var url = wpmflang.site_url;
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: {
                        action: "wpmf_change_view"
                    }, success: function (response) {
                        window.location.href = url;
                    }
                });
            });

            $('.view-grid,.view-list').unbind('click').bind('click', function (e) {
                var url = $(this).attr('href');
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: {
                        action: "wpmf_remove_view"
                    }, success: function (response) {
                        window.location.href = url;
                    }
                });
            });

            $('.wpmf-display-media').unbind('click').bind('click', function () {
                var selectID = $('.wpmf-categories option:selected').data('id');
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: {
                        action: "display_media",
                        wpmf_display_media: ($(this).attr('checked') == 'checked') ? 'yes' : 'no'
                    },
                    success: function (response) {
                        if (page != 'table') {
                            if ($('.wpmf-display-media').attr('checked') == 'checked') {
                                $('.wpmf-filter-display-media option[value="yes"]').prop('selected', true).change();
                            } else {
                                $('.wpmf-filter-display-media option[value="all"]').prop('selected', true).change();
                            }

                            var dir = $('#jao').find('a[data-id="' + selectID + '"]').data('file');

                            if ($('.jaofiletree .directory[data-group!="' + wpmflang.wpmf_curent_userid + '"]').length > 0) {
                                if ($('.wpmf-display-media').attr('checked') == 'checked') {
                                    $('.jaofiletree .directory[data-group!="' + wpmflang.wpmf_curent_userid + '"]').hide();
                                } else {
                                    $('.jaofiletree .directory[data-group!="' + wpmflang.wpmf_curent_userid + '"]').show();
                                }
                            } else {
                                methods.close('/');
                                methods.open('/');
                            }
                            $('[id^="__wp-uploader-id-"]:visible .wpmf-categories option[data-id="0"]').prop('selected', true).change();
                        } else {
                            $('.wpmf-categories option[data-id="0"]').prop('selected', true).change();
                        }
                    }
                });
            });

            $(document).on('click', '#link-btn', function (event) {
                if(typeof wpLink != "undefined"){
                    wpLink.open('link-btn'); /* Bind to open link editor! */
                    $('#wp-link-backdrop').show();
                    $('#wp-link-wrap').show();
                    $('#url-field,#wp-link-url').closest('div').find('span').html('Link To');
                    $('#link-title-field').closest('div').hide();
                    $('.wp-link-text-field').hide();

                    $('#url-field,#wp-link-url').val($('.compat-field-wpmf_gallery_custom_image_link input.text').val());
                    if ($('.compat-field-gallery_link_target select').val() == '_blank') {
                        $('#link-target-checkbox,#wp-link-target').prop('checked', true);
                    } else {
                        $('#link-target-checkbox,#wp-link-target').prop('checked', false);
                    }
                }
            });

            $(document).on('click', '#wp-link-submit', function (event) {
                var attachment_id = $('.attachment-details').data('id');
                if (attachment_id == undefined)
                    attachment_id = $('#post_ID').val();
                var link = $('#url-field').val();
                if (link == undefined) {
                    link = $('#wp-link-url').val();
                } // version 4.2+

                var link_target = $('#link-target-checkbox:checked').val();
                if (link_target == undefined) {
                    link_target = $('#wp-link-target:checked').val();
                } // version 4.2+

                if (link_target == 'on') {
                    link_target = '_blank'
                } else {
                    link_target = '';
                }

                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: {
                        action: "update_link",
                        id: attachment_id,
                        link: link,
                        //title  : title,
                        link_target: link_target
                    },
                    success: function (response) {
                        $('.compat-field-wpmf_gallery_custom_image_link input.text').val(response.link);
                        $('.compat-field-gallery_link_target select option[value="' + response.target + '"]').prop('selected', true).change();
                    }
                });
            });

            $('#wp-link-backdrop').appendTo($('body'));
            $('#wp-link-wrap').appendTo($('body'));

            $('input[id^="cb-select-all"]').on('click', function () {
                var checked = $('input[id^="cb-select-all"]').attr('checked');
                if (checked == 'checked') {
                    $('td.wpmf-move').addClass('selected');
                } else {
                    if ($('td.wpmf-move').hasClass('selected')) {
                        $('td.wpmf-move').removeClass('selected');
                    }
                }
            });

            $('.wpmf-attachment').unbind('click').bind('click', function (e) {
                if ($(this).hasClass('wpmfnoclick'))
                    return;
                if ($(e.target).hasClass('ui-draggable-dragging') || $(e.target).parents('.wpmf-attachment').hasClass('ui-draggable-dragging')) {
                    return;
                }
                var id = $(this).data('id');
                if (page != 'table') {
                    $('[id^="__wp-uploader-id-"]:visible .wpmf-categories [data-id="' + id + '"]').prop('selected', 'selected').change();
                } else {
                    $('.wpmf-categories [data-id="' + id + '"]').prop('selected', 'selected').change();
                }
            });

            //click on edit button
            $('.wpmf-attachment .wpmficon-edit').unbind('click').bind('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                name = prompt(wpmflang.promt, $(this).siblings('.filename').find('div').html());

                if (name !== '' && name != 'null') {
                    id = $(e.target).parents('li.wpmf-attachment').data('id');
                    parent_id = $(e.target).parents('li.wpmf-attachment').data('parent_id');

                    $.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: {
                            action: "edit_folder",
                            name: name,
                            id: id,
                            parent_id: parent_id,
                        },
                        success: function (response) {
                            if (response == false) {
                                if (name != wpmflang.wpmf_categories[id].label) {
                                    alert(wpmflang.alert_add);
                                }
                            } else {
                                if (typeof (response.term_id) !== 'undefined') {
                                    $('select.wpmf-categories option[data-id="' + id + '"]').html(response.name);
                                    $(e.target).parent('.wpmf-attachment-preview').find('.filename > div').html(response.name);
                                    wpmflang.wpmf_categories[id].label = response.name;
                                }

                                $('.directory[data-id="' + id + '"] a[data-id="' + id + '"]').html(name);
                                if (page != 'table') {
                                    $('[id^="__wp-uploader-id-"]:visible .wpmf-order-folder').change();
                                } else {
                                    $('.wpmf-order-folder').change();
                                }
                            }
                        }
                    });
                }
            });

            //click on delete button
            $('.wpmf-attachment .wpmficon-delete').unbind('click').bind('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof wpmflang.wpmf_remove_media != "undefined" && wpmflang.wpmf_remove_media == 1) {
                    var alert_delete = wpmflang.alert_delete_all;
                } else {
                    var alert_delete = wpmflang.alert_delete;
                }
                if (confirm(alert_delete)) {
                    var id = $(e.target).parents('li.wpmf-attachment').data('id');
                    var parent = $('.wpmf-categories option:selected').data('id');
                    if (typeof wpmflang.wpmf_remove_media != "undefined" && wpmflang.wpmf_remove_media == 1) {
                        $('.wpmf-attachment[data-id="' + id + '"]').css({'opacity': '0.5'});
                        $('.wpmf-attachment[data-id="' + id + '"] .filename').css({'background-image': 'url(' + wpmflang.wpmf_images_path + '/loading1.gif)', 'background-size': '100% 100%'})
                    }
                    $.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: {
                            action: "delete_folder",
                            id: id,
                            parent: parent
                        },
                        success: function (response) {
                            if (response.type == 'one') {
                                if (response.status) {
                                    $('select.wpmf-categories option[data-id="' + id + '"]').remove();
                                    $('.wpmf-attachment[data-id="' + id + '"]').remove();
                                    $('.directory[data-id="' + id + '"]').remove();
                                    if (response.count_child == 1) {
                                        $('.directory[data-id="' + parent + '"] .icon-open-close[data-id="' + parent + '"]').css({'opacity': 0});
                                    } else {
                                        $('.directory[data-id="' + parent + '"] .icon-open-close[data-id="' + parent + '"]').css({'opacity': 1});
                                    }
                                    delete(wpmflang.wpmf_categories[id]);
                                    var index = wpmflang.wpmf_categories_order.indexOf(id.toString());
                                    wpmflang.wpmf_categories_order.splice(index, 1);

                                    if (page != 'table') {
                                        wp.Uploader.queue.reset();
                                    }

                                } else {
                                    alert(wpmflang.alert_delete1);
                                }
                            } else {
                                jQuery.each(response.fids, function (i, fid) {
                                    $('.wpmf-attachment[data-id="' + fid + '"]').remove();
                                    $('.directory[data-id="' + fid + '"]').remove();
                                    $('select.wpmf-categories option[data-id="' + fid + '"]').remove();
                                    delete(wpmflang.wpmf_categories[fid]);
                                    if (page != 'table') {
                                        wp.Uploader.queue.reset();
                                    }
                                });
                            }
                        }
                    });
                }
            });
        };
        
        if (typeof wp != "undefined") {
            if (wp.media && $('body.upload-php table.media').length === 0) {
                if (wp.media.view.AttachmentFilters == undefined || wp.media.view.AttachmentsBrowser == undefined)
                    return;
                initOwnFilter = function () {
                    wp.media.view.AttachmentFilters['wpmf_categories'] = wp.media.view.AttachmentFilters.extend({
                        className: 'wpmf-categories attachment-filters',
                        id: 'wpmf-media-category',
                        createFilters: function () {
                            var filters = {};
                            var ij = 0;
                            space = '&nbsp;&nbsp;';
                            _.each(wpmflang.wpmf_categories_order || [], function (key) {

                                term = wpmflang.wpmf_categories[key];
                                var query = {};
                                query = {
                                    taxonomy: wpmflang.taxo,
                                    term_id: parseInt(term.id, 10),
                                    term_slug: term.slug,
                                    wpmf_taxonomy: 'true',
                                };
                                if (typeof term.depth == 'undefined') {
                                    term.depth = 0;
                                }
                                filters[ ij ] = {
                                    text: space.repeat(term.depth) + term.label,
                                    props: query
                                };

                                relCategoryFilter[term.id] = ij;
                                relFilterCategory[ij] = term.id;
                                ij++;
                            });

                            this.filters = filters;
                        }

                    });

                    wp.media.view.AttachmentFilters['wpmf_attachment_mimetype'] = wp.media.view.AttachmentFilters.extend({
                        className: 'attachment-filters attachment-mimetype',
                        id: 'media-attachment-size-mimetype',
                        createFilters: function () {
                            var filters = {};
                            if (wpmflang.wpmf_pagenow == 'post.php') {
                                filters['uploaded'] = {
                                    text: wpmflang.uploaded_to_this + wpmflang.wpmf_type,
                                    props: {
                                        uploadedTo: wp.media.view.settings.post.id,
                                        orderby: 'menuOrder',
                                        order: 'ASC'
                                    },
                                };
                            }

                            _.each(wpmflang.wpmf_post_mime_type || [], function (text, key) {
                                filters[ key ] = {
                                    text: text[0],
                                    props: {
                                        status: null,
                                        type: key,
                                        uploadedTo: null,
                                        orderby: false
                                    },
                                };
                            });

                            filters['unattached'] = {
                                text: 'Unattached',
                                props: {
                                    status: null,
                                    type: '',
                                    uploadedTo: null,
                                    orderby: false
                                },
                            };


                            filters.all = {
                                text: wpmflang.mimetype,
                                props: {
                                    status: null,
                                    type: null,
                                    uploadedTo: null,
                                    orderby: false
                                },
                                priority: 10
                            };

                            this.filters = filters;
                        },
                        change: function () {

                            var filter = this.filters[ this.el.value ];
                            if (this.el.value == 'uploaded') {
                                wpmfcheckMimetype = true;
                                $('[id^="__wp-uploader-id-"]:visible .wpmf-attachments-browser').hide();
                                // refreshWpmf();
                            } else {
                                wpmfcheckMimetype = false;
                                $('[id^="__wp-uploader-id-"]:visible .wpmf-attachments-browser').show();
                            }

                            if (filter) {
                                this.model.set(filter.props);
                            }
                        },
                    });

                    var myDrop = wp.media.view.AttachmentsBrowser;

                    wp.media.view.AttachmentsBrowser = wp.media.view.AttachmentsBrowser.extend({
                        
                        createToolbar: function () {
                            wp.media.model.Query.defaultArgs.filterSource = 'filter-attachment-category';
                            myDrop.prototype.createToolbar.apply(this, arguments);
                            //Save the attachments because we'll need it to change the category filter
                            usedAttachmentsBrowser = this;

                            this.toolbar.set('mimetypetags', new wp.media.view.AttachmentFilters['wpmf_attachment_mimetype']({
                                controller: this.controller,
                                model: this.collection.props,
                                priority: -90
                            }).render());

                            this.toolbar.set(wpmflang.taxo, new wp.media.view.AttachmentFilters['wpmf_categories']({
                                controller: this.controller,
                                model: this.collection.props,
                                priority: -75
                            }).render()
                                    );
                        }
                    });

                    if (usedAttachmentsBrowser !== null) {
                        usedAttachmentsBrowser.toolbar.set(wpmflang.taxo, new wp.media.view.AttachmentFilters['wpmf_categories']({
                            controller: usedAttachmentsBrowser.controller,
                            model: usedAttachmentsBrowser.collection.props,
                            priority: -75
                        }).render()
                                );
                        initSelectFilter();
                    }
                };
                if (page != 'table') {
                    initOwnFilter();
                }
                
                wp.media.view.AttachmentsBrowser.prototype.on('ready', function () {
                    refreshWpmf();
                });
                
                myMediaUploaderStatus = wp.media.view.UploaderStatus;
                wp.media.view.UploaderStatus = wp.media.view.UploaderStatus.extend({
                    progress: function () {
                        myMediaUploaderStatus.prototype.progress.apply(this, arguments);
                        var queue = this.queue,
                                $bar = this.$bar;

                        if (!$bar || !queue.length) {
                            return;
                        }

                        var wpmf_percent = (queue.reduce(function (memo, attachment) {
                            if (!attachment.get('uploading')) {
                                return memo + 100;
                            }

                            var percent = attachment.get('percent');
                            return memo + (_.isNumber(percent) ? percent : 100);
                        }, 0) / queue.length) + '%';
                        $('.wpmf-attachments-wrapper li.attachment.uploading .media-progress-bar > div').css({'width': wpmf_percent});
                    },
                    error: function (error) {
                        if (error.get('message') == wpmflang.error_replace) {
                            $('.upload-errors').addClass('wpmferror_replace');
                            wp.Uploader.queue.reset();
                        }
                        myMediaUploaderStatus.prototype.error.apply(this, arguments);

                    },
                });

                if (typeof wpmflang.usegellery != "undefined" && wpmflang.usegellery == 1) {
                    myMediaViewToolbar = wp.media.view.Toolbar;
                    if (typeof myMediaViewToolbar != "undefined") {
                        wp.media.view.Toolbar = wp.media.view.Toolbar.extend({
                            refresh: function () {
                                myMediaViewToolbar.prototype.refresh.apply(this, arguments);
                                var state = this.controller.state(),
                                        selection = state.get('selection');
                                if (state == undefined || selection == undefined) {
                                    return;
                                } else {
                                    if (selection.length == 0) {
                                        $('.btn-selectall,.btn-selectall1').show();
                                        $('.media-button-gallery').hide();
                                    } else {
                                        $('.btn-selectall,.btn-selectall1').hide();
                                        $('.media-button-gallery').show();
                                    }
                                }
                            }
                        });
                    }
                }

                applybtnreplace = wp.media.view.Toolbar;
                if (typeof applybtnreplace != "undefined") {
                    wp.media.view.Toolbar = wp.media.view.Toolbar.extend({
                        refresh: function () {
                            applybtnreplace.prototype.refresh.apply(this, arguments);
                        }
                    });
                }

                myMediaControllerLibrary = wp.media.controller.Library;
                wp.media.controller.Library = wp.media.controller.Library.extend({
                    refreshContent: function () {
                        initAttachments();
                        myMediaControllerLibrary.prototype.refreshContent.apply(this, arguments);
                    },
                });
                
                myMediaViewMenuItem = wp.media.view.Menu;
                wp.media.view.Menu = wp.media.view.Menu.extend({
                    select: function (id) {
                        myMediaViewMenuItem.prototype.select.apply(this, arguments);
                        if(id == 'iframe:wpmfdbx' || id == 'iframe:wpmfgg'){
                            $('.media-frame-menu .wpmf_jao').hide();
                        }else{
                            $('.media-frame-menu .wpmf_jao').show();
                        }
                    },
                });

                myMediaControllerCollectionEdit = wp.media.controller.CollectionEdit;
                wp.media.controller.CollectionEdit = wp.media.controller.CollectionEdit.extend({
                    activate: function () {
                        myMediaControllerCollectionEdit.prototype.activate.apply(this, arguments);
                        wpmfNextIsGallery = true;
                    },
                    deactivate: function () {
                        myMediaControllerCollectionEdit.prototype.deactivate.apply(this, arguments);
                        wpmfNextIsGallery = false;
                    }
                });

                if (typeof wpmflang.wpmf_pagenow != "undefined" && wpmflang.wpmf_pagenow == 'upload.php' && page !== 'table') {
                    mySelectModeToggle = wp.media.view.SelectModeToggleButton;
                    if (typeof mySelectModeToggle != "undefined") {
                        wp.media.view.SelectModeToggleButton = wp.media.view.SelectModeToggleButton.extend({
                            toggleBulkEditHandler: function () {
                                wpmf_move_fi = this;
                                mySelectModeToggle.prototype.toggleBulkEditHandler.apply(this, arguments);
                            }
                        });
                    }
                }

                myMediaControllerGalleryEdit = wp.media.controller.GalleryEdit;
                wp.media.controller.GalleryEdit = wp.media.controller.GalleryEdit.extend({
                    activate: function () {
                        myMediaControllerGalleryEdit.prototype.activate.apply(this, arguments);
                        wpmfNextIsGallery = true;
                    },
                    deactivate: function () {
                        myMediaControllerGalleryEdit.prototype.deactivate.apply(this, arguments);
                        wpmfNextIsGallery = false;
                    },
                    gallerySettings: function (browser) {
                        myMediaControllerGalleryEdit.prototype.gallerySettings.apply(this, arguments);
                        var library = this.get('library');
                        browser.toolbar.set('wpmf_reverse_gallery', {
                            text: 'Order by',
                            priority: 70,
                            click: function () {
                                var lists_i = library.toArray();
                                var listsId = [];
                                var wpmf_orderby = $('.wpmf_orderby').val();
                                var wpmf_order = $('.wpmf_order').val();
                                $.each(lists_i, function (i, v) {
                                    listsId.push(v.id);
                                });

                                var wpmf_img_order = [];
                                $.ajax({
                                    method: "POST",
                                    dataType: 'json',
                                    url: ajaxurl,
                                    data: {
                                        action: "wpmf_gallery_get_image",
                                        ids: listsId,
                                        wpmf_orderby: wpmf_orderby,
                                        wpmf_order: wpmf_order
                                    },
                                    success: function (res) {
                                        if (res != false) {
                                            $.each(res, function (i, v) {
                                                $.each(lists_i, function (k, h) {
                                                    if (h.id == v.ID)
                                                        wpmf_img_order.push(h);
                                                });
                                            });

                                            library.reset(wpmf_img_order);
                                        }
                                    }
                                });
                            }
                        });
                    }

                });

                if (typeof wpmflang.wpmf_search != "undefined" && wpmflang.wpmf_search == 1) {
                    mymediaviewsearch = wp.media.view.Search;
                    if (typeof mymediaviewsearch != "undefined") {
                        wp.media.view.Search = wp.media.view.Search.extend({
                            search: function (event) {
                                if (event.target.value) {
                                    $('.wpmf-attachments-browser').hide();
                                } else {
                                    $('.wpmf-attachments-browser').show();
                                }
                                mymediaviewsearch.prototype.search.apply(this, arguments);
                            }
                        });
                    }
                }

                myMediaViewModal = wp.media.view.Modal;
                wp.media.view.Modal = wp.media.view.Modal.extend({
                    open: function () {
                        myMediaViewModal.prototype.open.apply(this, arguments);
                        if (this.options.controller.options.state === 'gallery-edit' || (this.options.controller.options.state === 'insert' && this.options.controller._state === 'gallery-edit')) {
                            wpmfNextIsGallery = true;
                        } else {
                            wpmfNextIsGallery = false;
                        }

                        refreshWpmf();
                    }
                });

                //see http://stackoverflow.com/questions/14279786/how-to-run-some-code-as-soon-as-new-image-gets-uploaded-in-wordpress-3-5-uploade
                if (typeof wp.Uploader !== 'undefined' && typeof wp.Uploader.queue !== 'undefined') {
                    wp.Uploader.queue.on('reset', function () {
                        if (wpmflang.wpmf_post_type == 1 && $('#wpb_visual_composer').is(":visible")) {

                        } else {
                            $('.attachment.uploading').remove();
                            if (wp.media.frame.content.get() !== null) {
                                if (typeof wp.media.frame.content.get().collection != "undefined") {
                                    wp.media.frame.content.get().collection.props.set({ignore: (+new Date())});
                                    wp.media.frame.content.get().options.selection.reset();
                                }
                            } else {
                                wp.media.frame.library.props.set({ignore: (+new Date())});
                            }
                        }
                        $('select.wpmf-categories option[data-id="' + currentCategory + '"]').prop('selected', 'selected');
                    });
                } else {
                    return;
                }

                wp.Uploader.queue.on('add', function () {
                    if (wpmflang.wpmf_pagenow == 'upload.php' && page != 'table') {
                        if ($('.media-modal-content').length > 0) {
                            if ($('#wpmfreplace').hasClass('button-wpmfcancel')) {
                                $('body').addClass('wpmf-replace');
                            }
                            $('.media-modal-close').click();
                        }
                    }

                    if (wpmflang.wpmf_post_type == 1 && $('#wpb_visual_composer').is(":visible")) {

                    } else {
                        if ($('[id^="__wp-uploader-id-"]:visible .wpmf-attachments-wrapper .attachment').length == 0) {
                            $('[id^="__wp-uploader-id-"]:visible .wpmf-attachments-wrapper .attachments').append('<li class="attachment uploading"><div class="attachment-preview js--select-attachment type-image subtype-jpeg portrait"><div class="thumbnail"><div class="media-progress-bar"><div style="width:0%"></div></div></div></div></li>');
                        } else {
                            $('[id^="__wp-uploader-id-"]:visible .wpmf-attachments-wrapper .attachments .attachment:first-child').before('<li class="attachment uploading"><div class="attachment-preview js--select-attachment type-image subtype-jpeg portrait"><div class="thumbnail"><div class="media-progress-bar"><div style="width:0%"></div></div></div></div></li>');
                        }
                    }

                    //change the current folder
                    selectedId = $('.select_folder_id').val();
                    //save the current folder 
                    $.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: {
                            action: "change_folder",
                            id: selectedId
                        }
                    });

                });

            } else {
                if (typeof wpmflang.wpmf_categories == "undefined")
                    return;
                if ($('.wp-filter').length === 0)
                    return;
                //table mode
                page = 'table';
                var ij = 0;
                $.each(wpmflang.wpmf_categories || {}, function () {
                    relCategoryFilter[this.id] = ij;
                    relFilterCategory[ij] = this.id;
                    ij++;
                });

                if ($('.wpmf-attachments-wrapper').length === 0) {
                    $('.wpmf-attachments-browser, .wpmf-breadcrumb').remove();
                    //add the folders
                    $('.wp-list-table.media').before('<div class="wpmf-attachments-browser"></div><div class="wpmf-clear"></div>');

                    //wrapall 
                    $('.wpmf-breadcrumb, .wpmf-attachments-browser,.wpmf-clear').wrapAll('<div class="wpmf-attachments-wrapper"></div>');

                    //add the breadcrumb
                    $('.wpmf-attachments-wrapper').prepend('<ul class="wpmf-breadcrumb"><li><a href="#" data-id="0">Files</a></li></ul>');
                }

                //Add the drag column on table
                $('.wp-list-table.media thead tr,.wp-list-table.media tfoot tr').prepend('<th class="wpmf-move-header"></th>');
                $('.wp-list-table.media #the-list tr').prepend('<td class="wpmf-move" title="' + wpmflang.dragdrop + '"><span class="zmdi zmdi-more"></span></td>');

                initSelectFilter();
                addFolder();
                changeCategory.call($('select.wpmf-categories'));
            }
        }
    });
    //http://stackoverflow.com/questions/202605/repeat-string-javascript
    String.prototype.repeat = function (num) {
        return new Array(isNaN(num) ? 1 : ++num).join(this);
    };
}(jQuery));