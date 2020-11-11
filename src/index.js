import { registerBlockType } from "@wordpress/blocks";
import { Fragment } from "@wordpress/element";
import { RichText, InnerBlocks } from "@wordpress/block-editor";
import { Button, ToggleControl, __experimentalNumberControl as NumberControl, SelectControl } from "@wordpress/components";

import { useInstanceId } from "@wordpress/compose";

import { __ } from "@wordpress/i18n";

import "./style.scss";

const BlockEdit = (props) => {
	const { title, required, category, blockId } = props.attributes;

	const { className, setAttributes } = props;

	const instanceId = useInstanceId(BlockEdit);

	setAttributes({ blockId: `todo-${instanceId}` });

	const onChangeTitle = (value) => {
		setAttributes({ title: value });
	};

	const onChangeRequired = (value) => {
		setAttributes({ required: value });
	};

	const categories = wp.data.select("core").getEntityRecords("taxonomy", "todos_categories");

	let categories_for_select = [];

	categories.forEach((cat) => {
		categories_for_select.push({ value: cat.slug, label: cat.name });
	});

	return (
		<Fragment>
			<div className={className}>
				<div className='top-section-wrapper'>
					<div className='checkbox-wrapper'>
						<input type='checkbox' id={blockId} class='todo-checked' />
					</div>

					<div className='title-wrapper'>
						<RichText tagName='h3' value={title} className='todo-title' onChange={onChangeTitle} placeholder='Title goes here' />

						<div className='todo-controls-wrapper'>
							<div className='todo-control'>
								<ToggleControl label='Required?' checked={required} onChange={onChangeRequired} className='required' />
							</div>

							<div className='todo-control'>
								{category ? (
									<ul className='category-preview'>
										{category.map(function (name) {
											return <li className='category-preview-item'>{name}</li>;
										})}
									</ul>
								) : (
									" "
								)}
								<label className='todo-label category-label'>
									Question Category
									<SelectControl
										value={category}
										className='category-dropdown'
										multiple
										options={categories_for_select}
										onChange={(category) => {
											setAttributes({ category: category });
										}}
									/>
								</label>
							</div>
						</div>
					</div>

					<div class='todo-expand'>
						<span class='icon-chevron' aria-hidden='true'>
							<svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' viewBox='0 0 24 24'>
								<path xmlns='http://www.w3.org/2000/svg' fill-rule='evenodd' clip-rule='evenodd' d='M5.29289 9.29289C5.68342 8.90237 6.31658 8.90237 6.70711 9.29289L12 14.5858L17.2929 9.29289C17.6834 8.90237 18.3166 8.90237 18.7071 9.29289C19.0976 9.68342 19.0976 10.3166 18.7071 10.7071L12.7071 16.7071C12.3166 17.0976 11.6834 17.0976 11.2929 16.7071L5.29289 10.7071C4.90237 10.3166 4.90237 9.68342 5.29289 9.29289Z'></path>
							</svg>
						</span>
					</div>
				</div>

				<div className='content-wrapper'>
					<InnerBlocks />
				</div>
			</div>
		</Fragment>
	);
};

registerBlockType("umbcundergrad/todos", {
	description: __("Undergraduate Admissions Todo List", "umbcundergradtodos"),

	title: __("Undergraduate Admissions Todo List", "umbcundergradtodos"),

	category: "widgets",
	attributes: {
		title: {
			type: "string",
			source: "html",
			selector: ".todo-title",
		},

		required: {
			type: "boolean",
			default: true,
		},

		category: {
			type: "array",
		},

		blockId: {
			type: "string",
		},
	},

	icon: "yes-alt",

	supports: {
		html: false,
	},

	edit: BlockEdit,

	save: (props) => {
		const { title, required, category, blockId } = props.attributes;

		const { className } = props;

		let temp_categories = [];

		if (category !== "undefined") {
			category.forEach((cat) => {
				temp_categories.push(`category-${cat}`);
			});
		}

		temp_categories.push("todo-wrapper");
		temp_categories = temp_categories.join(" ");

		return (
			<div className={temp_categories}>
				<div className='top-section-wrapper'>
					<div className='checkbox-wrapper'>
						<input type='checkbox' id={blockId} class='todo-checked' />
					</div>

					<div className='title-wrapper'>
						<RichText.Content tagName='h3' value={title} className='todo-title' />
					</div>

					<button class='todo-expand'>
						<span class='icon-chevron' aria-hidden='true'>
							<svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' viewBox='0 0 24 24'>
								<path xmlns='http://www.w3.org/2000/svg' fill-rule='evenodd' clip-rule='evenodd' d='M5.29289 9.29289C5.68342 8.90237 6.31658 8.90237 6.70711 9.29289L12 14.5858L17.2929 9.29289C17.6834 8.90237 18.3166 8.90237 18.7071 9.29289C19.0976 9.68342 19.0976 10.3166 18.7071 10.7071L12.7071 16.7071C12.3166 17.0976 11.6834 17.0976 11.2929 16.7071L5.29289 10.7071C4.90237 10.3166 4.90237 9.68342 5.29289 9.29289Z'></path>
							</svg>
						</span>
					</button>
				</div>

				<div className='content-wrapper'>
					<InnerBlocks.Content />
				</div>
			</div>
		);
	},
});
